<?php


namespace App\Http\Controllers\Web;

use App\Exceptions\CoreException;
use App\Models\ClassGroupSyllabuses;
use App\Rules\UUID;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid as RamseyUUID;

/**
 *
 * @Date 04/06/21
 */
class ClassGroupSyllabusController extends StaffBaseController
{
    /**
     * @return Response|mixed
     */
    public function getAllClassGroups()
    {
        try {
            $classGroupSyllabus = ClassGroupSyllabuses::with("syllabus", "classGroups", "classGroups.userClasses", "classGroups.userClasses.course")->get();
            return $this->sendResponse($classGroupSyllabus, "Class group syllabuses fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function assignClassGroupSyllabus(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                "class_group_id" => ['required', new UUID()],
                'syllabus_id' => ['required', new UUID()]
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Invalid request sent.', $errors);
            }
//            $classGroupSyllabus = ClassGroupSyllabuses::where("class_group_id", "=", $input['class_group_id'])
//                ->where("syllabus_id", "=", $input['syllabus_id'])
//                ->first();
//            if ($classGroupSyllabus) {
//                return $this->sendJsonError("You are already mapped this class group and syllabus. You can manage the subjects from the below list");
//            }
            $userId = $request->user()->user_id;
            $input['created_by'] = $userId;
            $input['class_group_syllabus_id'] = RamseyUUID::uuid4();
            $classGroupSyllabus = ClassGroupSyllabuses::create($input);
            return $this->sendResponse($classGroupSyllabus, "Class group successfully assigned to syllabus");
        } catch (\Exception $e) {
            if ($e->getCode() === CoreException::INTEGRITY_CONSTRAINT_ERROR) {
                return $this->sendJsonError("You are already mapped this class group and syllabus. You can manage the subjects from the below list");
            }
            return $this->sendJsonError($e->getMessage());

        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function deleteClassGroupSyllabus(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            "class_group_syllabus_id" => ['required', new UUID()]
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        try {
            $classGroupSyllabus = ClassGroupSyllabuses::find($input['class_group_syllabus_id']);
            $classGroupSyllabus->delete();
            return $this->sendResponse(null, "Class group syllabus mapping removed successfully");
        } catch (\Exception $e) {
            if ($e->getCode() === CoreException::INTEGRITY_CONSTRAINT_ERROR) {
                return $this->sendJsonError("You can't delete this class group-syllabus mapping because this may have subjects and subscription months. Remove all those subjects and subscription months before trying again", [], CoreException::INTEGRITY_CONSTRAINT_ERROR);
            }
            return $this->sendJsonError($e->getMessage());
        }
    }

}
