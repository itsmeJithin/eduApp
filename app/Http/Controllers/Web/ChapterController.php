<?php


namespace App\Http\Controllers\Web;

use App\Exceptions\CoreException;
use App\Models\Chapters;
use App\Models\ClassGroupSyllabusSubjects;
use App\Models\Subjects;
use App\Models\SyllabusSubscribedMonthTopics;
use App\Models\SyllabusSubscriptionMonths;
use App\Models\Topics;
use App\Rules\UUID;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 *
 * @Date 06/06/21
 */
class ChapterController extends StaffBaseController
{
    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function getAllChapters(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                "class_group_syllabus_id" => ['required', new UUID()],
                "subject_id" => ['required', new UUID()]
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Invalid request sent.', $errors);
            }
            $chapters = ClassGroupSyllabusSubjects::with("chapters")->where("class_group_syllabus_id", "=", $input['class_group_syllabus_id'])
                ->where("subject_id", "=", $input['subject_id'])
                ->first();
            return $this->sendResponse($chapters, "Chapters fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function createOrUpdate(Request $request)
    {
        try {
            $userId = $request->user()->staff_user_id;
            $input = $request->all();
            $chapter = null;
            if (isset($input['chapter_id']) && !empty($input['chapter_id'])) {
                $validator = Validator::make($request->all(), [
                    'chapter_name' => 'required|min:3',
                    'chapter_code' => 'required|min:3',
                    'class_group_syllabus_id' => ['required', new UUID()],
                    'subject_id' => ['required', new UUID()],
                    'chapter_id' => ['required', new UUID()]
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Invalid details given', $errors);
                }
                $chapter = Chapters::find($input['chapter_id']);
                if (!$chapter) {
                    return $this->sendJsonError("Chapter not found", [], CoreException::CHAPTER_NOT_FOUND);
                }
                $isCodeExist = Chapters::where("chapter_code", "=", $input['chapter_code'])
                    ->where("chapter_id", "!=", $chapter->chapter_id)->first();
                if ($isCodeExist) {
                    return $this->sendJsonError("Duplicate chapter code", [], CoreException::DUPLICATE_CHAPTER_CODE);
                }
                $chapter->chapter_name = $input['chapter_name'];
                $chapter->chapter_code = $input['chapter_code'];
                $chapter->chapter_description = $input['chapter_description'];
                $chapter->updated_by = $userId;
                $chapter->save();
                return $this->sendResponse($chapter, "Chapter updated successfully");
            } else {
                $validator = Validator::make($request->all(), [
                    'chapter_name' => 'required|min:3',
                    'chapter_code' => 'required|min:3|unique:subjects,subject_code',
                    'class_group_syllabus_id' => ['required', new UUID()],
                    'subject_id' => ['required', new UUID()],
                ]);
                $input['created_by'] = $userId;
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Fill all the fields with valid data.', $errors);
                }
                $cgsSubject = ClassGroupSyllabusSubjects::where("subject_id", "=", $input['subject_id'])
                    ->where("class_group_syllabus_id", "=", $input['class_group_syllabus_id'])
                    ->first();
                if (empty($cgsSubject)) {
                    return $this->sendJsonError("Subjects group mapping not available", "error");
                }
                $input['class_group_syllabus_subject_id'] = $cgsSubject->class_group_syllabus_subject_id;
                $input['chapter_id'] = \Ramsey\Uuid\Uuid::uuid4();
                $class = Chapters::create($input);
                return $this->sendResponse($class, "Chapter created successfully");
            }

        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function deleteChapter(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'chapter_id' => ['required', new UUID()],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        try {
            $chapter = Chapters::find($input['chapter_id']);
            $chapter->delete();
            return $this->sendResponse(null, "Chapter deleted successfully");
        } catch (\Exception $e) {
            if ($e->getCode() === CoreException::INTEGRITY_CONSTRAINT_ERROR) {
                return $this->sendJsonError("You can't delete this chapter because this chapter contains topics. Remove all those topics from the selected chapter before trying again", [], CoreException::INTEGRITY_CONSTRAINT_ERROR);
            }
            return $this->sendJsonError($e->getMessage());
        }
    }

    public function getAllSubscriptionMonthChapters(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class_group_syllabus_id' => ['required', new UUID()],
            "class_group_syllabus_subject_id" => 'required|int',
            "subscription_month_id" => ['required', new UUID()]
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        try {
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            $subscriptionMonthId = $input['subscription_month_id'];
            $syllabusSubscriptionMonth = $this->getSyllabusSubscriptionId($classGroupSyllabusId, $subscriptionMonthId);
            if ($syllabusSubscriptionMonth) {
                $ssmTopics = SyllabusSubscribedMonthTopics::where("syllabus_subscription_month_id", "=", $syllabusSubscriptionMonth->syllabus_subscription_month_id)
                    ->where("is_active", "=", 1)
                    ->whereNull("deleted_at")
                    ->first();
                $chapters = $ssmTopics->chapters()->get();
                return $this->sendResponse($chapters, "Chapters fetched successfully");
            } else {
                return $this->sendJsonError("Invalid request sent");
            }
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
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

}
