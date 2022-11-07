<?php


namespace App\Http\Controllers\Web;

use App\Exceptions\CoreException;
use App\Models\ClassGroupSyllabuses;
use App\Models\ClassGroupSyllabusSubjects;
use App\Models\Courses;
use App\Models\Subjects;
use App\Models\Syllabuses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

/**
 *
 * @Date 03/06/21
 */
class SubjectController extends StaffBaseController
{
    public function index()
    {
        return view("pages.subjects.subjects");
    }

    /**
     * filter subjects by class group id and syllabus id
     *
     * @param Request $request
     * @return Response|mixed
     */
    public function filterSubjects(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'class_group_id' => ['required', new \App\Rules\UUID],
            'syllabus_id' => ['required', new \App\Rules\UUID],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        try {
            $input = $request->all();
            $classGroupSyllabus = ClassGroupSyllabuses::where("class_group_id", "=", $input['class_group_id'])
                ->where("syllabus_id", "=", $input['syllabus_id'])
                ->first();
            if (!$classGroupSyllabus)
                return $this->sendJsonError("No subject is assigned to selected class group. You can use Design Group subjects menu option to create subjects");
            $subjects = $classGroupSyllabus->subjects()->get();
            foreach ($subjects as $subject) {
                $cgsSubject = ClassGroupSyllabusSubjects::where("subject_id", "=", $subject->subject_id)
                    ->where("class_group_syllabus_id", "=", $classGroupSyllabus->class_group_syllabus_id)
                    ->first();
                $subject->class_group_syllabus_subject_id = $cgsSubject->class_group_syllabus_subject_id;
            }
            return $this->sendResponse($subjects, "Subjects fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }

    }

    public function getAllSubjectsByClassGroupSyllabusId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_group_syllabus_id' => ['required', new \App\Rules\UUID],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        try {
            $input = $request->all();
            $subjects = ClassGroupSyllabuses::find($input['class_group_syllabus_id'])->subjects()->get();
            return $this->sendResponse($subjects, "Subjects fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAllFormListingItems(Request $request)
    {
        try {
            $courses = Courses::where("is_active", "=", 1)->get();
            $syllabuses = Syllabuses::where("is_active", "=", 1)->get();
            return $this->sendResponse(["courses" => $courses, "syllabuses" => $syllabuses], "Successfully fetched all required items");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return Response|mixed
     * @throws \Throwable
     */
    public function createOrUpdate(Request $request)
    {
        try {
            $userId = $request->user()->staff_user_id;
            $input = $request->all();
            $subject = null;
            if (isset($input['subject_id']) && !empty($input['subject_id'])) {
                $validator = Validator::make($request->all(), [
                    'subject_name' => 'required|min:3',
                    'subject_code' => 'required|min:3',
                    'class_group_syllabus_id' => ['required', new \App\Rules\UUID()],
                    'subject_id' => ['required', new \App\Rules\UUID()]
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $subject = Subjects::find($input['subject_id']);
                if (!$subject) {
                    return $this->sendJsonError("Subject not found", [], CoreException::COURSE_NOT_FOUND);
                }
                $isSubjectCodeExist = Subjects::where("subject_code", "=", $input['subject_code'])
                    ->where("subject_id", "!=", $subject->subject_id)->first();
                if ($isSubjectCodeExist) {
                    return $this->sendJsonError("Duplicate subject code", [], CoreException::DUPLICATE_SUBJECT_CODE);
                }
                $subject->subject_name = $input['subject_name'];
                $subject->subject_code = $input['subject_code'];
                $subject->subject_description = $input['subject_description'];
                $subject->updated_by = $userId;
                $subject->save();
                return $this->sendResponse($subject, "Subject updated successfully");
            } else {
                $validator = Validator::make($request->all(), [
                    'subject_name' => 'required|min:3',
                    'subject_code' => 'required|min:3|unique:subjects,subject_code',
                    'class_group_syllabus_id' => new \App\Rules\UUID(),
                ]);
                $input['created_by'] = $userId;
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Fill all the fields with valid data.', $errors);
                }
                $input['subject_id'] = Uuid::uuid4();
                DB::beginTransaction();
                $class = Subjects::create($input);
                ClassGroupSyllabusSubjects::create([
                    "class_group_syllabus_id" => $input['class_group_syllabus_id'],
                    "subject_id" => $input['subject_id'],
                ]);
                DB::commit();
                return $this->sendResponse($class, "Subject created successfully");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function deleteClassGroup(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'class_group_syllabus_id' => ['required', new \App\Rules\UUID()],
                'subject_id' => ['required', new \App\Rules\UUID()]
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Invalid request sent', $errors);
            }
            $classGroupSyllabuses = ClassGroupSyllabuses::where('subject_id', "=", $input['subject_id'])
                ->get();
            if ($classGroupSyllabuses && $classGroupSyllabuses->count() > 1) {
                $classGroupSyllabus = ClassGroupSyllabuses::where('subject_id', "=", $input['subject_id'])
                    ->where('class_group_syllabus_id', "=", $input['class_group_syllabus_id'])
                    ->get();
                $classGroupSyllabus->delete();
            } else {
                $classGroupSyllabus = ClassGroupSyllabuses::where('subject_id', "=", $input['subject_id'])
                    ->where('class_group_syllabus_id', "=", $input['class_group_syllabus_id'])
                    ->get();
                $classGroupSyllabus->delete();
                $subject = Subjects::find($input['subject_id']);
                $subject->delete();
            }

            return $this->sendResponse(null, "Subject deleted successfully");
        } catch (\Exception $e) {
            if ($e->getCode() === CoreException::INTEGRITY_CONSTRAINT_ERROR) {
                return $this->sendJsonError("You can't delete this subject because this subject already assigned to other class groups and also contain chapters. Remove all those class groups and chapters before trying again", [], CoreException::INTEGRITY_CONSTRAINT_ERROR);
            }
            return $this->sendJsonError($e->getMessage());
        }
    }

}
