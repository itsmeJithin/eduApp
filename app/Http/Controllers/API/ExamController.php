<?php


namespace App\Http\Controllers\API;

use App\Exceptions\CoreException;
use App\Models\CgsSubjectExamQuestions;
use App\Models\CgsSubjectExams;
use App\Models\ExamModes;
use App\Models\QuestionPool;
use App\Models\StudentERAnswers;
use App\Models\StudentExamRegistrations;
use App\Models\SyllabusSubscribedMonthTopics;
use App\Models\Topics;
use App\Models\UsersTopicsWatchList;
use App\Rules\UUID;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function Symfony\Component\Translation\t;

/**
 *
 * @Date 20/05/21
 */
class ExamController extends BaseController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function getAllExamsModes(Request $request)
    {
        try {
            $examModes = ExamModes::where("is_active", "=", "1")->get();
            return $this->sendResponse("Exam modes fetched successfully", $examModes);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * get exams
     *
     * @param Request $request
     * @return Response|mixed
     */
    public function getChapterWiseExams(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_mode_id' => 'required',
            'cgs_subject_id' => 'required',
            'subscription_month_id' => 'required',
            'chapter_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error . ', $errors);
        }
        try {
            $params = $request->all();
            $exam = CgsSubjectExams::where("cgs_subject_id", "=", $params['cgs_subject_id']);
            return $this->sendResponse("Exams fetched successfully", null);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

    }

    /**
     * @param Request $request
     */
    public function isUserCompletedGivenChapter(Request $request)
    {
        $input = $request->all();
        $userId = $request->user()->user_id;
        $validator = Validator::make($request->all(), [
            "chapter_id" => ["required", new UUID()],
            "subscription_month_id" => ["required", new UUID()],
            "cgs_subject_id" => "required",
            "class_group_syllabus_id" => ["required", new UUID()]
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error . ', $errors);
        }
        try {
            $chapterId = $input['chapter_id'];
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            $subscriptionMonthId = $input['subscription_month_id'];
            $status = $this->checkUserCanAttendExam($classGroupSyllabusId, $subscriptionMonthId, $userId, $chapterId);
            return $this->sendResponse($status, "Status check completed");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param $chapterId
     * @param $syllabusSubscriptionMonthId
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    private function getSubscriptionMonthChapters($chapterId, $syllabusSubscriptionMonthId)
    {
        try {
            return DB::table("topics as t")
                ->select("t.*")
                ->join("ss_month_topics as ssmt", "ssmt.topic_id", "=", "t.topic_id")
                ->where("t.chapter_id", "=", $chapterId)
                ->where("ssmt.syllabus_subscription_month_id", "=", $syllabusSubscriptionMonthId)
                ->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $chapterId
     * @param $userId
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    private function getChapterTopicsWatchList($chapterId, $userId)
    {
        try {
            return DB::table("topics as t")
                ->select("t.*")
                ->join("users_topics_watch_list as utwl", function ($query) use ($userId) {
                    $query->on("utwl.topic_id", "=", "t.topic_id")
                        ->where("utwl.user_id", "=", $userId);

                })
                ->where("t.chapter_id", "=", $chapterId)
                ->get();

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function getChapterExamModes(Request $request)
    {
        $input = $request->all();
        $userId = $request->user()->user_id;
        $validator = Validator::make($request->all(), [
            "chapter_id" => ["required", new UUID()],
            "subscription_month_id" => ["required", new UUID()],
            "cgs_subject_id" => "required",
            "class_group_syllabus_id" => ["required", new UUID()]
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error . ', $errors);
        }

        try {

            $chapterId = $input['chapter_id'];
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            $subscriptionMonthId = $input['subscription_month_id'];
            $cgsSubjectId = $input['cgs_subject_id'];
            if (!$this->checkUserCanAttendExam($classGroupSyllabusId, $subscriptionMonthId, $userId, $chapterId)) {
                return $this->sendJsonError("You should watch all the topics inside the selected chapter");
            }
            $examModes = DB::table("exam_modes as em")
                ->select("em.*")
                ->join("cgs_subject_exams as cse", "cse.exam_mode_id", "=", "em.exam_mode_id")
                ->where("chapter_id", "=", $chapterId)
                ->where("subscription_month_id", "=", $subscriptionMonthId)
                ->where("is_mock_test", "=", 0)
                ->where("is_published", "=", 1)
                ->where("is_chapter_wise", "=", 1)
                ->where("cgs_subject_id", "=", $cgsSubjectId)
                ->distinct()
                ->get();
            return $this->sendResponse($examModes, "Chapter exam modes fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getChapterExamByExamMode(Request $request)
    {
        $input = $request->all();
        $userId = $request->user()->user_id;
        $validator = Validator::make($request->all(), [
            "chapter_id" => ["required", new UUID()],
            "subscription_month_id" => ["required", new UUID()],
            "cgs_subject_id" => "required|int",
            "exam_mode_id" => "required|int",
            "class_group_syllabus_id" => ["required", new UUID()]
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error . ', $errors);
        }
        try {
            $chapterId = $input['chapter_id'];
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            $subscriptionMonthId = $input['subscription_month_id'];
            $cgsSubjectId = $input['cgs_subject_id'];
            $examModeId = $input['exam_mode_id'];
            $userSubscriptionId = checkUserSubscribed($classGroupSyllabusId, $subscriptionMonthId, $userId);
            if (!$userSubscriptionId) {
                return $this->sendJsonError("You are not subscribed this month");
            }
            if (!$this->checkUserCanAttendExam($classGroupSyllabusId, $subscriptionMonthId, $userId, $chapterId)) {
                return $this->sendJsonError("You should watch all the topics inside the selected chapter");
            }
            $exams = CgsSubjectExams::with("examMode")
                ->where("exam_mode_id", "=", $examModeId)
                ->where("chapter_id", "=", $chapterId)
                ->where("subscription_month_id", "=", $subscriptionMonthId)
                ->where("is_mock_test", "=", 0)
                ->where("is_published", "=", 1)
                ->where("is_chapter_wise", "=", 1)
                ->where("cgs_subject_id", "=", $cgsSubjectId)
                ->get();
            return $this->sendResponse($exams, "Exams fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    protected function checkUserCanAttendExam($classGroupSyllabusId, $subscriptionMonthId, $userId, $chapterId)
    {
        $syllabusSubscriptionMonth = getSyllabusSubscriptionId($classGroupSyllabusId, $subscriptionMonthId);
        if (!$syllabusSubscriptionMonth)
            return $this->sendJsonError("Invalid request");
        $userSubscriptionId = checkUserSubscribed($classGroupSyllabusId, $subscriptionMonthId, $userId);
        if (!$userSubscriptionId) {
            return $this->sendJsonError("You are not subscribed this month");
        }
        $chapterTopics = $this->getSubscriptionMonthChapters($chapterId, $syllabusSubscriptionMonth->syllabus_subscription_month_id);
        $topics = $this->getChapterTopicsWatchList($chapterId, $userId);
        if ($chapterTopics->count() === 0 || $topics->count() === 0)
            return false;
        return $chapterTopics->count() === $topics->count();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function examRegistration(Request $request)
    {
        $input = $request->all();
        $userId = $request->user()->user_id;
        $validator = Validator::make($request->all(), [
            "chapter_id" => ["required", new UUID()],
            "subscription_month_id" => ["required", new UUID()],
            "cgs_subject_id" => "required|int",
            "cgs_subject_exam_id" => ["required", new UUID()],
            "class_group_syllabus_id" => ["required", new UUID()]
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error . ', $errors);
        }
        try {
            $chapterId = $input['chapter_id'];
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            $subscriptionMonthId = $input['subscription_month_id'];
            $userSubscriptionId = checkUserSubscribed($classGroupSyllabusId, $subscriptionMonthId, $userId);
            $cgsExamId = $input['cgs_subject_exam_id'];
            if (!$userSubscriptionId) {
                return $this->sendJsonError("You are not subscribed this month");
            }
            if (!$this->checkUserCanAttendExam($classGroupSyllabusId, $subscriptionMonthId, $userId, $chapterId)) {
                return $this->sendJsonError("You should watch all the topics inside the selected chapter");
            }
            $data = $this->registerIfNotRegistered($cgsExamId, $userId);
            return $this->sendResponse($data, "You are registered for this exam successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function getExamQuestion(Request $request)
    {
        $input = $request->all();
        $userId = $request->user()->user_id;
        $validator = Validator::make($input, [
            "cgs_subject_exam_id" => ["required", new UUID()],
            "student_exam_registration_id" => ["required", new UUID()],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request', $errors);
        }
        try {
            $cgsSubjectExamId = $input['cgs_subject_exam_id'];
            $exam = CgsSubjectExams::find($cgsSubjectExamId);
            if (!$exam->isPublished()) {
                return $this->sendJsonError("Exam not available now");
            }
            $numberOfQuestions = $exam->numberOfQuestions()->get()->count();
            if (!$numberOfQuestions) {
                return $this->sendJsonError("This exam has no questions. Contact our support team inform this issue");
            }
            $examRegistration = StudentExamRegistrations::where('student_exam_registration_id', "=", $input['student_exam_registration_id'])
                ->where("user_id", "=", $userId)
                ->first();
            if (!$examRegistration)
                return $this->sendJsonError("Invalid request sent");
            if ($examRegistration->is_completed) {
                return $this->sendJsonError("You have already completed this exam");
            }
            $examAnswers = $examRegistration->questionAnswers()->first();
            if ($examAnswers) {
                $questionAnswers = $examAnswers->question_answers;
                if (!is_array($questionAnswers))
                    $questionAnswers = json_decode($questionAnswers);
                $lastAttendedQuestion = array_pop($questionAnswers);
                $lastQuestionOrder = CgsSubjectExamQuestions::where("cgs_subject_exam_id", "=", $cgsSubjectExamId)
                    ->where("question_id", "=", $lastAttendedQuestion['question_id'])
                    ->first();
                $order = $lastQuestionOrder->priority;
                if ($order === $numberOfQuestions) {
                    return $this->sendJsonError("You are already completed this examination");
                }
                ++$order;
                $nextExamQuestion = CgsSubjectExamQuestions::where("cgs_subject_exam_id", "=", $cgsSubjectExamId)
                    ->where("priority", "=", $order)
                    ->first()->question()->first();
                if ($nextExamQuestion) {
                    $options = [];
                    if (!is_array($nextExamQuestion->options))
                        $options = json_decode($nextExamQuestion->options);

                    foreach ($options as $option) {
                        unset($option->isRightAnswer);
                    }
                    $nextExamQuestion->options = $options;
                    $data = [
                        "next_question" => $nextExamQuestion,
                        "is_last_question" => $order === $numberOfQuestions
                    ];
                    return $this->sendResponse($data, "Question fetched successfully");
                } else {
                    return $this->sendJsonError("You have already completed this examination");
                }

            } else {
                $currentQuestion = $exam->questions()->first();
                $options = [];
                if (!is_array($currentQuestion->options))
                    $options = json_decode($currentQuestion->options);

                foreach ($options as $option) {
                    unset($option->isRightAnswer);
                }
                $currentQuestion->options = $options;
                $data = [
                    "next_question" => $currentQuestion,
                    "is_last_question" => $numberOfQuestions === 1
                ];
                return $this->sendResponse($data, "Question fetched successfully");
            }
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    public function markStudentExamAnswer(Request $request)
    {
        $input = $request->all();
        $userId = $request->user()->user_id;
        $validator = Validator::make($input, [
            "cgs_subject_exam_id" => ["required", new UUID()],
            "student_exam_registration_id" => ["required", new UUID()],
            "question_id" => ["required", new UUID()],
            "answer_id" => "nullable|numeric",
            "time_taken" => "nullable|int"
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request', $errors);
        }
        try {
            $cgsSubjectExamId = $input['cgs_subject_exam_id'];
            $exam = CgsSubjectExams::find($cgsSubjectExamId);
            $numberOfQuestions = $exam->numberOfQuestions()->get()->count();
            $question = $exam->questions()->where("question_pool.question_id", "=", $input['question_id'])->first();
            $isRightAnswer = false;
            $marks = 0;
            $options = [];
            if (!is_array($question->options))
                $options = json_decode($question->options);
            $rightAnswerId = null;
            $studentAnswerId = $input['answer_id'] ?? null;
            foreach ($options as $option) {
                if ($studentAnswerId && $option->id === (int)$studentAnswerId && $option->isRightAnswer) {
                    $marks = $question->mark;
                    $rightAnswerId = $option->id;
                    $isRightAnswer = true;
                    break;
                } elseif ($option->isRightAnswer) {
                    $rightAnswerId = $option->id;
                }
            }
            $questionAnswers = StudentExamRegistrations::find($input['student_exam_registration_id'])->questionAnswers()->first();
            $object['question_id'] = $input['question_id'];
            if (!$studentAnswerId) {
                $object['answer_id'] = null;
                $object['is_skipped'] = true;
            } else {
                $object['is_skipped'] = false;
                $object['answer_id'] = $studentAnswerId;
            }
            $object['time_taken'] = $input['time_taken'] ?? null;
            $object['is_right_answer'] = $isRightAnswer;
            if ($questionAnswers) {
                $userQuestionAnswers = $questionAnswers->question_answers;
                if (!is_array($userQuestionAnswers))
                    $userQuestionAnswers = json_decode($userQuestionAnswers);

                $marks = $questionAnswers->marks_obtained + $marks;
                $userQuestionAnswers[] = $object;
                $questionAnswers->question_answers = $userQuestionAnswers;
                $questionAnswers->marks_obtained = $marks;
                $questionAnswers->save();
            } else {
                StudentERAnswers::create([
                    "student_exam_registration_id" => $input['student_exam_registration_id'],
                    'question_answers' => [$object],
                    "marks_obtained" => $marks
                ]);
            }
            if ($question->pivot->priority === $numberOfQuestions) {
                $examRegistration = StudentExamRegistrations::find($input['student_exam_registration_id']);
                $examRegistration->is_completed = 1;
                $examRegistration->exam_completed_at = Carbon::now();
                $examRegistration->save();
            }
            if ($isRightAnswer) {
                $data = [
                    "is_right_answer" => true,
                    "right_answer_id" => $rightAnswerId,
                    "total_marks_obtained" => $marks
                ];
                return $this->sendResponse($data, "Congrats! You marked right answer");
            } else {
                $data = [
                    "is_right_answer" => false,
                    "right_answer_id" => $rightAnswerId,
                    "total_marks_obtained" => $marks
                ];
                return $this->sendResponse($data, "Oops! You marked wrong answer");
            }

        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAllLiveExams(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user->subscriptionMonths()->count()) {
                return $this->sendJsonError("You have not subscribed. You should at-least subscribe one month to access this feature", [], CoreException::NOT_SUBSCRIBED);
            }
            $currentDate = now()->setTimezone("UTC")->format("Y-m-d H:i:s");
            $exams = CgsSubjectExams::with("subject")->where("is_published", "=", 1)
                ->where("is_live_exam", "=", 1)
                ->where('start_date', "<=", $currentDate)
                ->where('end_date', ">=", $currentDate)
                ->orderBy('start_date', "DESC")
                ->get();
            return $this->sendResponse($exams, "Live exams fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    public function registerForLiveExams(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            "cgs_subject_exam_id" => ["required", new UUID()]
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error . ', $errors);
        }
        try {
            $user = $request->user();
            $userId = $user->user_id;
            if (!$user->subscriptionMonths()->count()) {
                return $this->sendJsonError("You have not subscribed. You should at-least subscribe one month to access this feature", [], CoreException::NOT_SUBSCRIBED);
            }
            $cgsExamId = $input['cgs_subject_exam_id'];
            $data = $this->registerIfNotRegistered($cgsExamId, $userId);
            return $this->sendResponse($data, "Registered successfully for exams");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param $cgsExamId
     * @param $userId
     * @return array
     */
    protected function registerIfNotRegistered($cgsExamId, $userId)
    {
        $examRegistration = StudentExamRegistrations::where("user_id", "=", $userId)
            ->where("cgs_subject_exam_id", "=", $cgsExamId)
            ->where("is_completed", "=", 0)
            ->first();
        if ($examRegistration) {
            return [
                "student_exam_registration_id" => $examRegistration->student_exam_registration_id,
                "is_previously_attended" => true
            ];

        }
        $examRegistration = StudentExamRegistrations::create([
            "student_exam_registration_id" => \Ramsey\Uuid\Uuid::uuid4(),
            "user_id" => $userId,
            "exam_started_at" => Carbon::now(),
            "cgs_subject_exam_id" => $cgsExamId,
            "status" => "IN_PROGRESS"
        ]);
        return [
            "student_exam_registration_id" => $examRegistration->student_exam_registration_id,
            "is_previously_attended" => false
        ];
    }

    public function getStudentExamResults(Request $request)
    {
        try {
            return $this->sendResponse(null, "Exam results fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getStudentRegisteredExams(Request $request)
    {
        $userId = $request->user()->user_id;
        try {
            $exams = StudentExamRegistrations::with(["exam", "examMark" ])
                ->where("user_id", "=", $userId)
                ->orderBy("created_at", "DESC")
                ->get();
            return $this->sendResponse($exams, "Exams fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }


}
