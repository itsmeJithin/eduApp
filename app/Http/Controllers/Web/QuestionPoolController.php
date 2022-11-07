<?php


namespace App\Http\Controllers\Web;

use App\Exceptions\CoreException;
use App\Models\QuestionPool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

/**
 *
 * @Date 10/06/21
 */
class QuestionPoolController extends StaffBaseController
{
    public function index()
    {
        return view("pages.exams.exams");
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAllSubjectQuestionPaper(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'class_group_syllabus_subject_id' => 'required|int',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Validation Error.', $errors);
            }
            $questions = QuestionPool::where("class_group_syllabus_subject_id", "=", $input['class_group_syllabus_subject_id'])
                ->whereNull("deleted_at")
                ->orderBy("created_at")
                ->get();
            return $this->sendResponse($questions, "Questions fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    public function saveQuestionDetails(Request $request)
    {
        $input = $request->all();
        $userId = $request->user()->staff_user_id;
        try {
            $question = null;
            if (isset($input['question_id'])) {
                $validator = Validator::make($request->all(), [
                    'question_id' => ['required', new \App\Rules\UUID()],
                    'question' => 'required|min:3',
                    'options' => 'required',
                    'class_group_syllabus_subject_id' => 'required|int',
                    'mark' => 'required|int',
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $answerOptions = json_decode($input['options']);
                if (!count($answerOptions)) {
                    return $this->sendJsonError("You should add at-least two options for this question");
                }
                $rightAnswerFound = false;
                foreach ($answerOptions as $option) {
                    if ($option->isRightAnswer) {
                        $rightAnswerFound = true;
                        break;
                    }
                }
                if (!$rightAnswerFound) {
                    return $this->sendJsonError("You should mark one option as right answer");
                }
                $question = QuestionPool::find($input['question_id']);
                if ($question) {
                    $question->question = $input['question'];
                    $question->options = $input['options'];
                    $question->mark = $input['mark'];
                    $question->question_time = $input['question_time'];
                    $question->updated_by = $userId;
                    $question->save();
                } else {
                    return $this->sendJsonError("This question may deleted by someone");
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'question' => 'required|min:3',
                    'options' => 'required',
                    'class_group_syllabus_subject_id' => 'required|int',
                    'mark' => 'required|int',
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $answerOptions = json_decode($input['options']);
                if (!count($answerOptions)) {
                    return $this->sendJsonError("You should add at-least two options for this question");
                }
                $rightAnswerFound = false;
                foreach ($answerOptions as $option) {
                    if ($option->isRightAnswer) {
                        $rightAnswerFound = true;
                        break;
                    }
                }
                if (!$rightAnswerFound) {
                    return $this->sendJsonError("You should mark one option as right answer");
                }
                $questionId = Uuid::uuid4();
                $question = QuestionPool::create([
                    "question_id" => $questionId,
                    "question_type_id" => 1,
                    "question" => $input['question'],
                    "options" => $input['options'],
                    "class_group_syllabus_subject_id" => $input['class_group_syllabus_subject_id'],
                    "mark" => $input['mark'],
                    "question_time" => $input['question_time'],
                    "created_by" => $userId
                ]);
            }
            return $this->sendResponse($question, "Question details saved successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    public function deleteQuestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id' => ['required', new \App\Rules\UUID()],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent', $errors);
        }
        try {
            $input = $request->all();
            $question = QuestionPool::find($input['question_id']);
            $question->delete();
            return $this->sendResponse(null, "Question deleted successfully");
        } catch (\Exception $e) {
            if ($e->getCode() === CoreException::INTEGRITY_CONSTRAINT_ERROR) {
                return $this->sendJsonError("You can't delete this question because this question added to some exams. Remove this question from all those exams before trying again", [], CoreException::INTEGRITY_CONSTRAINT_ERROR);
            }
            return $this->sendJsonError($e->getMessage());
        }
    }
}
