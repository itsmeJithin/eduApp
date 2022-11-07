<?php


namespace App\Http\Controllers\API;

use App\Exceptions\CoreException;
use App\Models\FavouriteTopics;
use App\Models\SyllabusSubscribedMonthDemoTopics;
use App\Models\SyllabusSubscriptionMonths;
use App\Models\TopicDoubts;
use App\Models\Topics;
use App\Models\UsersTopicsWatchList;
use App\Models\UserSubscribedSyllabusMonths;
use App\Rules\UUID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid as RamseyUuid;


/**
 *
 * @Date 09/08/20
 */
class TopicController extends BaseController
{
    public function getAllTopics(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'chapter_id' => new UUID(),
            'subscription_month_id' => new UUID(),
            "class_group_syllabus_id" => new UUID(),
            'syllabus_id' => new UUID()
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        $chapterId = $input['chapter_id'];
        $userId = $request->user()->user_id;
        $subscriptionMonthId = $input['subscription_month_id'];
        $classGroupSyllabusId = $input['class_group_syllabus_id'];
        $syllabusId = $input['syllabus_id'];
        try {
            if ($this->checkUserSubscribed($classGroupSyllabusId, $subscriptionMonthId, $userId)) {
                $topics = DB::table('topics')
                    ->select('topics.*')
                    ->join('ss_month_topics', 'ss_month_topics.topic_id', '=', 'topics.topic_id')
                    ->join("chapters", 'chapters.chapter_id', '=', 'topics.chapter_id')
                    ->join("class_group_syllabus_subjects AS cgss", "cgss.class_group_syllabus_subject_id", "=", "chapters.class_group_syllabus_subject_id")
                    ->join("class_group_syllabuses AS cgs", "cgs.class_group_syllabus_id", "=", "cgss.class_group_syllabus_id")
                    ->join("syllabus_subscription_months AS ssm", function ($join) {
                        $join->on("ssm.class_group_syllabus_id", "=", "cgs.class_group_syllabus_id")
                            ->on("ssm.syllabus_subscription_month_id", "=", "ss_month_topics.syllabus_subscription_month_id");
                    })
                    ->where('chapters.chapter_id', "=", $chapterId)
                    ->where('ssm.subscription_month_id', "=", $subscriptionMonthId)
                    ->where('cgs.syllabus_id', "=", $syllabusId)
                    ->where('ssm.is_active', "=", 1)
                    ->where('ss_month_topics.is_active', "=", 1)
                    ->where('chapters.is_active', "=", 1)
                    ->get();
                foreach ($topics as $topic) {
                    $favouriteTopic = FavouriteTopics::where("topic_id", "=", $topic->topic_id)
                        ->where("user_id", "=", $userId)
                        ->where("class_group_syllabus_id", "=", $classGroupSyllabusId)
                        ->where("subscription_month_id", "=", $subscriptionMonthId)
                        ->first();
                    if ($favouriteTopic) {
                        $topic->is_favourite = true;
                        $topic->favourite_topic_id = $favouriteTopic->favourite_topic_id;
                    } else {
                        $topic->is_favourite = false;
                        $topic->favourite_topic_id = null;
                    }

                }
                return $this->sendResponse($topics, "Topics retrieved successfully");
            }
            return $this->sendError("You are not subscribed");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), "ERROR", 200);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAllStudyMaterialsByTopicId(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'subscription_month_id' => new UUID(),
            "class_group_syllabus_id" => new UUID(),
            'topic_id' => new UUID()
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        $userId = $request->user()->user_id;
        $subscriptionMonthId = $input['subscription_month_id'];
        $classGroupSyllabusId = $input['class_group_syllabus_id'];
        $topicId = $input['topic_id'];
        try {

            if ($this->checkUserSubscribed($classGroupSyllabusId, $subscriptionMonthId, $userId)) {
                $studyMaterials = Topics::find($topicId)->studyMaterials()->get();
                return $this->sendResponse($studyMaterials, "Study materials are fetched successfully");
            }
            return $this->sendError("You are not subscribed");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), "ERROR", 200);
        }
    }

    /**
     * @param $classGroupSyllabusId
     * @param $subscriptionMonthId
     * @param $userId
     * @return bool|string
     */
    private function checkUserSubscribed($classGroupSyllabusId, $subscriptionMonthId, $userId)
    {
        $syllabusSubscriptionMonthId = SyllabusSubscriptionMonths::select("syllabus_subscription_month_id")
            ->where("class_group_syllabus_id", "=", $classGroupSyllabusId)
            ->where("subscription_month_id", "=", $subscriptionMonthId)
            ->value("syllabus_subscription_month_id");
        if (!empty($syllabusSubscriptionMonthId)) {
            return UserSubscribedSyllabusMonths::select("us_syllabus_month_id")
                ->where("syllabus_subscription_month_id", "=", $syllabusSubscriptionMonthId)
                ->where("class_group_syllabus_id", "=", $classGroupSyllabusId)
                ->where("user_id", "=", $userId)
                ->value("us_syllabus_month_id");
        }
        return false;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function askDoubts(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class_group_syllabus_id' => new UUID(),
            'subscription_month_id' => new UUID(),
            'doubt' => 'required|min:10',
            'topic_id' => new UUID()
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        try {
            $subscriptionMonthId = $input['subscription_month_id'];
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            $userId = $request->user()->user_id;
            if (!$this->checkUserSubscribed($classGroupSyllabusId, $subscriptionMonthId, $userId)) {
                return $this->sendJsonError("You are not subscribed");
            }
            $input['created_by'] = $userId;
            $input['updated_by'] = $userId;
            $input['topic_doubt_id'] = RamseyUuid::uuid4();
            $doubt = TopicDoubts::create($input);
            return $this->sendResponse($doubt, "You doubt details added successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAllTopicDoubts(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'topic_id' => new UUID(),
            'class_group_syllabus_id' => new UUID(),
            'subscription_month_id' => new UUID(),
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        try {
            $userId = $request->user()->user_id;
            $subscriptionMonthId = $input['subscription_month_id'];
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            if (!$this->checkUserSubscribed($classGroupSyllabusId, $subscriptionMonthId, $userId)) {
                return $this->sendJsonError("You are not subscribed");
            }
            $topicDoubts = Topics::find($input['topic_id'])
                ->doubts()
                ->with("doubtAnswers")
                ->where("created_by", "=", $userId)
                ->get();
            return $this->sendResponse($topicDoubts, "topic doubts fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function markTopicAsFavourite(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class_group_syllabus_id' => new UUID(),
            'subscription_month_id' => new UUID(),
            'topic_id' => new UUID()
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        try {
            $userId = $request->user()->user_id;
            $subscriptionMonthId = $input['subscription_month_id'];
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            if (!checkUserSubscribed($classGroupSyllabusId, $subscriptionMonthId, $userId)) {
                return $this->sendJsonError("You are not subscribed");
            }
            $favTopic = FavouriteTopics::create([
                "user_id" => $userId,
                "subscription_month_id" => $input['subscription_month_id'],
                "class_group_syllabus_id" => $input['class_group_syllabus_id'],
                'topic_id' => $input['topic_id'],
                "created_by" => $userId
            ]);
            return $this->sendResponse($favTopic, "Topic marked as favourite");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function unmarkTopicAFromFavourite(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class_group_syllabus_id' => new UUID(),
            'subscription_month_id' => new UUID(),
            'topic_id' => new UUID()
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        try {
            $userId = $request->user()->user_id;
            $subscriptionMonthId = $input['subscription_month_id'];
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            if (!checkUserSubscribed($classGroupSyllabusId, $subscriptionMonthId, $userId)) {
                return $this->sendJsonError("You are not subscribed");
            }
            FavouriteTopics::where("topic_id", "=", $input['topic_id'])
                ->where("user_id", "=", $userId)
                ->where("subscription_month_id", "=", $subscriptionMonthId)
                ->where("class_group_syllabus_id", "=", $classGroupSyllabusId)
                ->delete();
            return $this->sendResponse(null, "Topic removed from favourite list");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAllFavouriteTopics(Request $request)
    {
        $userId = $request->user()->user_id;
        try {
            $favoriteTopics = FavouriteTopics::with("topic", "topic.chapter")
                ->where("user_id", "=", $userId)->get();
            return $this->sendResponse($favoriteTopics, "Favorite topics fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function addOrUpdateTopicWatchList(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class_group_syllabus_id' => new UUID(),
            'topic_id' => new UUID(),
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        try {
            $watchTime = isset($input['watch_time']) ? $input['watch_time'] : 0;
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            $topicId = $input['topic_id'];
            $userId = $request->user()->user_id;
            $topic = UsersTopicsWatchList::where("user_id", "=", $userId)
                ->where("class_group_syllabus_id", "=", $classGroupSyllabusId)
                ->where("topic_id", "=", $topicId)
                ->first();
            if ($topic && $watchTime) {
                $topic->watch_time = $input['watch_time'];
                $topic->save();
            } elseif (!$topic) {
                $topic = UsersTopicsWatchList::create([
                    "user_id" => $userId,
                    "class_group_syllabus_id" => $classGroupSyllabusId,
                    "topic_id" => $topicId,
                    "watch_time" => $watchTime
                ]);
            }
            return $this->sendResponse($topic, "topic added to watched list");

        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAllWatchList(Request $request)
    {
        $userId = $request->user()->user_id;
        try {
            $topics = DB::table("topics as t")
                ->select("t.*", "c.chapter_name", "c.chapter_id", "s.subject_id", "s.subject_name",
                    "cgss.class_group_syllabus_subject_id", "smt.syllabus_subscription_month_id",
                    "cgss.class_group_syllabus_id")
                ->join("users_topics_watch_list as utwl", "utwl.topic_id", "=", "t.topic_id")
                ->join("ss_month_topics as smt", "smt.topic_id", "=", "t.topic_id")
                ->join("chapters as c", "c.chapter_id", "=", "t.chapter_id")
                ->join("class_group_syllabus_subjects as cgss", "cgss.class_group_syllabus_subject_id", "=", "c.class_group_syllabus_subject_id")
                ->join("subjects as s", "s.subject_id", "=", "cgss.subject_id")
                ->where("utwl.user_id", "=", $userId)
                ->orderBy("utwl.updated_at")
                ->get();
            return $this->sendResponse($topics, "watch history fetched successfully");

        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getSubjectDemoTopic(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class_group_syllabus_subject_id' => 'required|int'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors, CoreException::VALIDATION_ERRORS);
        }
        try {
            $demoTopic = SyllabusSubscribedMonthDemoTopics::where("class_group_syllabus_subject_id", "=", $input['class_group_syllabus_subject_id'])
                ->where("is_active", "=", 1)
                ->first();
            return $this->sendResponse($demoTopic, "Demo topic content fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }
}
