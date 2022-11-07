<?php


namespace App\Http\Controllers\Web;

use App\Helpers\S3Util;
use App\Models\AptuResources;
use App\Models\StudyMaterials;
use App\Models\SyllabusSubscribedMonthTopics;
use App\Models\SyllabusSubscriptionMonths;
use App\Models\Topics;
use App\Rules\UUID;
use App\Rules\YoutubeURL;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 *
 * @Date 06/06/21
 */
class TopicController extends StaffBaseController
{
    public function getAllTopics(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'chapter_id' => ['required', new UUID()],
                'subscription_month_id' => ['required', new UUID()],
                'class_group_syllabus_id' => ['required', new UUID()],
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Invalid request sent.', $errors);
            }
            $chapterId = $input['chapter_id'];
            $subscriptionMonthId = $input['subscription_month_id'];
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            $topics = DB::table('topics')
                ->select('topics.*', 'ss_month_topics.priority', 'ss_month_topics.ss_month_topic_id')
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
                ->where('cgs.class_group_syllabus_id', "=", $classGroupSyllabusId)
                ->where('ssm.is_active', "=", 1)
                ->where('ss_month_topics.is_active', "=", 1)
                ->where('chapters.is_active', "=", 1)
                ->orderBy("ss_month_topics.priority")
                ->get();
            return $this->sendResponse($topics, "Topics fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function updateTopicOrder(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'topics' => 'array'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        try {
            $userId = $request->user()->user_id;
            $topics = $input['topics'];
            $i = 0;
            foreach ($topics as $topic) {
                ++$i;
                $monthTopic = SyllabusSubscribedMonthTopics::find($topic);
                $monthTopic->priority = $i;
                $monthTopic->updated_by = $userId;
                $monthTopic->save();
            }
            return $this->sendResponse(null, "Order updated successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    public function createOrUpdate(Request $request)
    {
        $input = $request->all();
        $topic = null;
        try {
            $userId = $request->user()->staff_user_id;
            if (isset($input['topic_id']) && !empty($input['topic_id'])) {
                $validator = Validator::make($input, [
                    "class_group_syllabus_id" => ['required', new UUID()],
                    "subject_id" => ['required', new UUID()],
                    "chapter_id" => ['required', new UUID()],
                    "topic_id" => ['required', new UUID()],
                    "subscription_month_id" => ['required', new UUID()],
                    "topic_name" => 'required|min:3',
                    "topic_code" => 'required|min:3',
                    'video_url' => ['required', new YoutubeURL()]
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError("Invalid data submitted", $errors);
                }
                $topic = Topics::find($input['topic_id']);
                if (empty($topic)) {
                    return $this->sendJsonError("Topic not available now. Refresh your window to get all updates");
                }
                $isCodeExist = Topics::where("topic_code", "=", $input['topic_code'])
                    ->where("topic_id", "!=", $topic->topic_id)
                    ->first();
                if ($isCodeExist) {
                    return $this->sendJsonError("Topic code already taken. Use another topic code");
                }
                $topic->topic_name = $input['topic_name'];
                $topic->topic_code = $input['topic_code'];
                $topic->topic_description = $input['topic_description'];
                $topic->video_url = $input['video_url'];
                $topic->save();
                $studyMaterials = [];
                if (isset($input['study_materials'])) {
                    $studyMaterials = json_decode($input['study_materials']);
                }
                if (count($studyMaterials)) {
                    $this->addTopicMaterials($topic->topic_id, $studyMaterials, $userId);
                }
            } else {
                $validator = Validator::make($input, [
                    "class_group_syllabus_id" => ['required', new UUID()],
                    "subject_id" => ['required', new UUID()],
                    "chapter_id" => ['required', new UUID()],
                    "subscription_month_id" => ['required', new UUID()],
                    "topic_name" => 'required|min:3',
                    "topic_code" => 'required|min:3|unique:topics,topic_code',
                    'video_url' => ['required', new YoutubeURL()]
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError("Invalid data submitted", $errors);
                }
                $syllabusSubscription = $this->getSyllabusSubscriptionId($input['class_group_syllabus_id'], $input['subscription_month_id']);
                if (empty($syllabusSubscription)) {
                    return $this->sendError("Invalid request sent!");
                }
                $priority = SyllabusSubscribedMonthTopics::where("syllabus_subscription_month_id", "=", $syllabusSubscription->syllabus_subscription_month_id)
                    ->max('priority');
                if ($priority)
                    ++$priority;
                else $priority = 1;
                $topicId = \Ramsey\Uuid\Uuid::uuid4();
                $topic = [
                    "topic_id" => $topicId,
                    "topic_name" => $input['topic_name'],
                    "topic_code" => $input['topic_code'],
                    "topic_description" => $input['topic_description'],
                    "video_url" => $input['video_url'],
                    "chapter_id" => $input['chapter_id'],
                    "created_by" => $userId
                ];

                $topic = Topics::create($topic);
                $ssMonth = SyllabusSubscribedMonthTopics::create([
                    "syllabus_subscription_month_id" => $syllabusSubscription->syllabus_subscription_month_id,
                    "topic_id" => $topicId,
                    "priority" => ++$priority
                ]);
                $studyMaterials = [];
                if (isset($input['study_materials'])) {
                    $studyMaterials = json_decode($input['study_materials']);
                }
                if (count($studyMaterials)) {
                    $this->addTopicMaterials($topicId, $studyMaterials, $userId);
                }
            }
            return $this->sendResponse($topic, "Topic details saved successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }

    }

    private function addTopicMaterials($topicId, $materials, $userId)
    {
        foreach ($materials as $material) {
            $resourceId = \Ramsey\Uuid\Uuid::uuid4();
            $resource = AptuResources::create([
                "aptu_resource_id" => $resourceId,
                "file_name" => $material->name,
                "storage_object" => json_encode($material),
                "created_by" => $userId,
                "updated_by" => $userId
            ]);

            $studyMaterial = StudyMaterials::create([
                "study_material_id" => \Ramsey\Uuid\Uuid::uuid4(),
                "study_material_name" => $material->name,
                "topic_id" => $topicId,
                "resource_id" => $resourceId,
                "created_by" => $userId
            ]);
        }
    }

    /**
     * @param $classGroupSyllabusId
     * @param $subscriptionMonthId
     * @return mixed
     * @throws \Exception
     */
    private function getSyllabusSubscriptionId($classGroupSyllabusId, $subscriptionMonthId)
    {
        try {
            return SyllabusSubscriptionMonths::where("class_group_syllabus_id", "=", $classGroupSyllabusId)
                ->where("subscription_month_id", "=", $subscriptionMonthId)
                ->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Throwable
     */
    public function deleteTopic(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'topic_id' => ['required', new UUID()],
            'class_group_syllabus_id' => ['required', new UUID()],
            "chapter_id" => ['required', new UUID()],
            "subscription_month_id" => ['required', new UUID()],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        try {
            $topic = Topics::find($input['topic_id']);
            if ($topic) {
                $ssMonth = $this->getSyllabusSubscriptionId($input['class_group_syllabus_id'], $input['subscription_month_id']);
                $ssTopic = SyllabusSubscribedMonthTopics::where("syllabus_subscription_month_id", "=", $ssMonth->syllabus_subscription_month_id)
                    ->where("topic_id", "=", $input['topic_id'])
                    ->first();
                if ($ssTopic)
                    $ssTopic->delete();
                $studyMaterials = $topic->studyMaterials()->get();
                foreach ($studyMaterials as $material) {
                    $resourceId = $material->resource_id;
                    $material->delete();
                    if ($resourceId) {
                        $s3Util = new S3Util();
                        $s3Util->deleteFile($resourceId);
                    }
                }
                $topic->delete();
            } else {
                return $this->sendJsonError("Topic not found. Refresh your window to get updated");
            }
            return $this->sendResponse(null, "Topic deleted successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function deleteStudyMaterial(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'study_material_id' => ['required', new UUID()],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        try {
            $studyMaterial = StudyMaterials::find($input['study_material_id']);
            if (empty($studyMaterial)) {
                return $this->sendJsonError("Study material not available. Refresh your window to get updates");
            }
            if (!empty($studyMaterial->resource_id)) {
                $s3Util = new S3Util();
                $s3Util->deleteFile($studyMaterial->resource_id);
            }
            $studyMaterial->delete();
            return $this->sendResponse(null, "Study material deleted successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function getAllTopicStudyMaterials(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'topic_id' => ['required', new UUID()],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        $studyMaterials = [];
        try {
            $topic = Topics::find($input['topic_id']);
            if ($topic) {
                $studyMaterials = $topic->studyMaterials()->get();
                foreach ($studyMaterials as $studyMaterial) {
                    if (!empty($studyMaterial->study_material_url))
                        continue;
                    $s3Util = new S3Util();
                    $response = $s3Util->getPreSignedURL($studyMaterial->resource_id);
                    $studyMaterial->study_material_url = $response->url;
                }
            }
            return $this->sendResponse($studyMaterials, "Study materials fetched successfully");

        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }
}
