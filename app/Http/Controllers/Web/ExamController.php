<?php


namespace App\Http\Controllers\Web;

use App\Exceptions\CoreException;
use App\Models\CgsSubjectExamQuestions;
use App\Models\CgsSubjectExams;
use App\Models\ExamModes;
use App\Models\QuestionPool;
use App\Models\StudentExamRegistrations;
use App\Rules\UUID;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 *
 * @Date 10/06/21
 */
class ExamController extends StaffBaseController
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view("pages.exams.exams");
    }

    public function manageExamModes()
    {
        return view("pages.exams.exams");
    }

    /**
     * returns all exam modes
     *
     * @return Response|mixed
     */
    public function getAllExamModes()
    {
        try {
            $examModes = ExamModes::where("is_active", "=", 1)->get();
            return $this->sendResponse($examModes, "Exam modes fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * Creating or update exam modes
     *
     * @param Request $request
     * @return mixed
     */
    public function createOrUpdateExamModes(Request $request)
    {
        $input = $request->all();
        $examMode = null;
        try {
            if (isset($input['exam_mode_id'])) {
                $validator = Validator::make($request->all(), [
                    'exam_mode_name' => 'required|min:3',
                    'exam_mode_code' => 'required|min:3',
                    'exam_mode_id' => 'required|int'
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $examMode = ExamModes::find($input['exam_mode_id']);
                if (!$examMode) {
                    return $this->sendJsonError("Exam mode not found", [], CoreException::EXAM_MODE_NOT_FOUND);
                }
                $isModeCodeExist = ExamModes::where("exam_mode_code", "=", $input['exam_mode_code'])
                    ->where("exam_mode_id", "!=", $examMode->exam_mode_id)->first();
                if ($isModeCodeExist) {
                    return $this->sendJsonError("Duplicate Exam mode code", [], CoreException::DUPLICATE_EXAM_MODE_CODE);
                }
                $examMode->exam_mode_name = $input['exam_mode_name'];
                $examMode->exam_mode_code = $input['exam_mode_code'];
                $examMode->exam_mode_description = $input['exam_mode_description'];
                $examMode->save();
            } else {
                $validator = Validator::make($request->all(), [
                    'exam_mode_name' => 'required|min:3',
                    'exam_mode_code' => 'required|min:3|unique:exam_modes,exam_mode_code',
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $examMode = ExamModes::create($input);
            }
            return $this->sendResponse($examMode, "Exam mode details saved successfully");

        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function createOrUpdateExam(Request $request)
    {
        $input = $request->all();
        $exam = null;
        try {
            if (isset($input['cgs_subject_exam_id'])) {
                $validator = Validator::make($request->all(), [
                    "class_group_syllabus_subject_id" => "required|int",
                    "class_group_syllabus_id" => ["required", new UUID()],
                    "cgs_subject_exam_id" => ["required", new UUID()],
                    "exam_name" => "required|min:4",
                    "exam_mode" => "nullable|int",
                    "is_mock_test" => "required|boolean",
                    "is_live_exam" => "required|boolean",
                    "is_monthly_chapter_exam" => "required|boolean",
                    "subscription_month_id" => ["nullable", new UUID()],
                    "chapter_id" => ["nullable", new UUID()]
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $exam = CgsSubjectExams::find($input['cgs_subject_exam_id']);
                if ($exam) {
                    $isLiveExam = $input['is_live_exam'];
                    $isMockTest = $input['is_mock_test'];
                    $isMonthlyChapterExam = $input['is_monthly_chapter_exam'];
                    $exam->subject_exam_name = $input['exam_name'];
                    $exam->subject_exam_description = $input['exam_description'];
                    $exam->maximum_marks = $input['maximum_marks'];
                    $exam->maximum_time = $input['maximum_time'];
                    if ($isMockTest) {
                        $exam->is_mock_test = true;
                        $exam->subscription_month_id = null;
                        $exam->chapter_id = null;
                        $exam->is_chapter_wise = false;
                        $exam->exam_mode_id = null;
                        $exam->is_live_exam = false;
                    } elseif ($isMonthlyChapterExam) {
                        $exam->is_mock_test = false;
                        $exam->subscription_month_id = $input['subscription_month_id'];
                        $exam->chapter_id = $input['chapter_id'];
                        $exam->is_chapter_wise = true;
                        $exam->is_live_exam = false;
                        $exam->exam_mode_id = $input['exam_mode'];
                    } elseif ($isLiveExam) {
                        $exam->is_live_exam = true;
                        $exam->is_mock_test = false;
                        $exam->subscription_month_id = null;
                        $exam->chapter_id = null;
                        $exam->is_chapter_wise = false;
                        $exam->exam_mode_id = null;
                        $exam->start_date = $input['start_date'];
                        $exam->end_date = $input['end_date'];;
                    } else {
                        $exam->is_live_exam = false;
                        $exam->is_mock_test = false;
                        $exam->subscription_month_id = null;
                        $exam->chapter_id = null;
                        $exam->is_chapter_wise = false;
                        $exam->exam_mode_id = null;
                    }
                    $exam->save();
                } else {
                    return $this->sendJsonError("Exam not found. Refresh your window to get latest updates");
                }
            } else {
                $validator = Validator::make($request->all(), [
                    "class_group_syllabus_subject_id" => "required|int",
                    "class_group_syllabus_id" => ["required", new UUID()],
                    "exam_name" => "required|min:4",
                    "exam_mode" => "nullable|int",
                    "is_mock_test" => "required|boolean",
                    "is_monthly_chapter_exam" => "required|boolean",
                    "is_live_exam" => "required|boolean",
                    "subscription_month_id" => ["nullable", new UUID()],
                    "chapter_id" => ["nullable", new UUID()],
                    "maximum_marks" => "required",
                    "maximum_time" => "required"
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $isLiveExam = $input['is_live_exam'];
                $isMockTest = $input['is_mock_test'];
                $isMonthlyChapterExam = $input['is_monthly_chapter_exam'];
                $data = [
                    "cgs_subject_exam_id" => \Ramsey\Uuid\Uuid::uuid4(),
                    "cgs_subject_id" => $input['class_group_syllabus_subject_id'],
                    "subject_exam_name" => $input['exam_name'],
                    "subject_exam_description" => $input['exam_description'],
                    "maximum_marks" => $input['maximum_marks'],
                    "maximum_time" => $input['maximum_time']
                ];
                if ($isMockTest) {
                    $data["is_mock_test"] = true;
                } elseif ($isLiveExam) {
                    $data["is_live_exam"] = true;
                    $data['start_date'] = $input['start_date'];
                    $data['end_date'] = $input['end_date'];
                } elseif ($isMonthlyChapterExam) {
                    $data['subscription_month_id'] = $input['subscription_month_id'];
                    $data['chapter_id'] = $input['chapter_id'];
                    $data['exam_mode_id'] = $input['exam_mode'];
                    $data['is_chapter_wise'] = true;
                }
                $exam = CgsSubjectExams::create($data);
            }
            return $this->sendResponse($exam, "Exam details saved successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function getExams(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            "class_group_syllabus_subject_id" => "required|int"
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors);
        }
        try {
            $exams = CgsSubjectExams::with("chapter", "subscriptionMonth", "examMode", "numberOfQuestions")
                ->withCount("questions")
                ->where("cgs_subject_id", "=", $input['class_group_syllabus_subject_id'])
                ->orderBy("created_at", "DESC")
                ->get();
            return $this->sendResponse($exams, "Exams fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAssignedAndAvailableExamQuestions(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($request->all(), [
                "class_group_syllabus_subject_id" => "required|int",
                "exam_id" => ["required", new UUID()]
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Validation Error.', $errors);
            }
            $exam = CgsSubjectExams::with("questions")->where("cgs_subject_exam_id", "=", $input['exam_id'])
                ->first();
            $availableQuestions = QuestionPool::where("class_group_syllabus_subject_id", "=", $input['class_group_syllabus_subject_id'])
                ->get();
            $data = [
                "exam" => $exam,
                "availableQuestions" => $availableQuestions
            ];
            return $this->sendResponse($data, "Successfully fetched");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function manageExamQuestions(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            "exam_id" => ["required", new UUID()],
            "assigned_questions" => "nullable|array",
            "deleted_questions" => "nullable|array"
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors);
        }
        try {
            $deletedQuestions = $input['deleted_questions'];
            $assignedQuestions = $input['assigned_questions'];
            $examId = $input['exam_id'];
            if (count($deletedQuestions) && !count($assignedQuestions)) {
                return $this->sendJsonError("You have not did any changes. You can assign on remove questions from this exam");
            }
            $exam = CgsSubjectExams::find($examId);
            if ($exam && $exam->is_published) {
                return $this->sendJsonError("You can not edit published exams. If you want to edit, you need to un-publish the selected exam");
            } elseif (!$exam) {
                return $this->sendJsonError("Exam not found. You need to refresh your window");
            }
            CgsSubjectExamQuestions::where("cgs_subject_exam_id", "=", $examId)
                ->whereIn("question_id", $deletedQuestions)
                ->delete();
            $index = 0;
            foreach ($assignedQuestions as $question) {
                ++$index;
                $examQuestion = CgsSubjectExamQuestions::where("cgs_subject_exam_id", "=", $examId)
                    ->where("question_id", "=", $question)
                    ->first();
                if ($examQuestion) {
                    $examQuestion->priority = $index;
                    $examQuestion->save();
                } else {
                    CgsSubjectExamQuestions::updateOrCreate([
                        "cgs_subject_exam_id" => $examId,
                        "question_id" => $question,
                        "priority" => $index
                    ]);
                }

            }
            return $this->sendResponse(null, "Changes applied successfully");
        } catch (\Exception $e) {
            if ($e->getCode() === CoreException::INTEGRITY_CONSTRAINT_ERROR) {
                $this->sendJsonError("You can not delete questions from this exam because this exam is already attended by students");
            }
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function deleteExam(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            "cgs_subject_exam_id" => ["required", new UUID()],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors);
        }
        try {
            $examId = $input['cgs_subject_exam_id'];
            $exam = CgsSubjectExams::find("$examId");
            if ($exam) {
                $exam->examQuestions()->delete();
                $exam->delete();
                return $this->sendResponse(null, "Exam and it's questions deleted successfully");
            } else {
                return $this->sendJsonError("The exam going to be deleted is not found. May be some others already deleted this exam. Refresh your window to get latest updates");
            }

        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function toggleExamStatus(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            "cgs_subject_exam_id" => ["required", new UUID()],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors);
        }
        try {
            $exam = CgsSubjectExams::find($input['cgs_subject_exam_id']);
            if (!$exam->numberOfQuestions()->count() && !$exam->is_published) {
                return $this->sendJsonError("You should add at-least one question to this exam before publishing this exam");
            }
            $exam->is_published = !$exam->is_published;
            $exam->save();
            return $this->sendResponse(null, "Exam status changed successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAllExamResults(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            "cgs_subject_exam_id" => ["required", new UUID()],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors);
        }
        try {
            $examResults = StudentExamRegistrations::with("user", "examMark")
                ->where("cgs_subject_exam_id", "=", $input['cgs_subject_exam_id'])
                ->orderBy("created_at", "DESC")
                ->get();
            return $this->sendResponse($examResults, "Exam results fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }
}
