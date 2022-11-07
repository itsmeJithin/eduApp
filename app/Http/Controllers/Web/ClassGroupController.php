<?php


namespace App\Http\Controllers\Web;

use App\Exceptions\CoreException;
use App\Models\Classes;
use App\Models\ClassGroups;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

/**
 *
 * @Date 04/06/21
 */
class ClassGroupController extends StaffBaseController
{
    public function index()
    {
        return view("pages.classGroups.classGroups");
    }

    /**
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAllClassGroups()
    {
        try {
            $classGroups = ClassGroups::with("userClasses", "userClasses.course")
                ->where("is_active", "=", "1")
                ->orderBy("updated_at", "DESC")
                ->orderBy("created_at", "DESC")
                ->get();
            return $this->sendResponse($classGroups, "Class groups fetched successfully");
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
            $classGroup = null;
            if (isset($input['class_group_id']) && !empty($input['class_group_id'])) {
                $validator = Validator::make($request->all(), [
                    'class_group_name' => 'required|min:3',
                    'class_group_code' => 'required|min:3',
                    'class_group_id' => ['required', new \App\Rules\UUID()],
                    'class_id' => ['required', new \App\Rules\UUID()]
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Invalid details given. Fill all required fields with valid data', $errors);
                }
                $classGroup = ClassGroups::find($input['class_group_id']);
                if (!$classGroup) {
                    return $this->sendJsonError("Course not found", [], CoreException::COURSE_NOT_FOUND);
                }
                $isCodeExist = ClassGroups::where("class_group_code", "=", $input['class_group_code'])
                    ->where("class_group_id", "!=", $classGroup->class_group_id)->first();
                if ($isCodeExist) {
                    return $this->sendJsonError("Duplicate course code", [], CoreException::DUPLICATE_CLASS_CODE);
                }
                $classGroup->class_group_name = $input['class_group_name'];
                $classGroup->class_group_code = $input['class_group_code'];
                $classGroup->class_group_description = $input['class_group_description'];
                $classGroup->class_id = $input['class_id'];
                $classGroup->updated_by = $userId;
                $classGroup->save();
                return $this->sendResponse($classGroup, "Class groups updated successfully");
            } else {
                $validator = Validator::make($request->all(), [
                    'class_group_name' => 'required|min:3',
                    'class_group_code' => 'required|min:3|unique:class_groups,class_group_code',
                    'class_id' => ['required', new \App\Rules\UUID()]
                ]);
                $input['created_by'] = $userId;
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $input['class_group_id'] = Uuid::uuid4();
                $class = ClassGroups::create($input);
                return $this->sendResponse($class, "Class groups created successfully");
            }

        } catch (\Exception $e) {
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
                'class_group_id' => new \App\Rules\UUID()
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Validation Error.', $errors);
            }
            $class = ClassGroups::find($input['class_group_id']);
            $class->delete();
            return $this->sendResponse(null, "Class group deleted successfully");
        } catch (\Exception $e) {
            if ($e->getCode() === CoreException::INTEGRITY_CONSTRAINT_ERROR) {
                return $this->sendJsonError("You can't delete this class because this class groups contains subjects. Remove all those subjects before trying again", [], CoreException::INTEGRITY_CONSTRAINT_ERROR);
            }
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAllClassGroupsByClass(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'class_id' => new \App\Rules\UUID()
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Validation Error.', $errors);
            }
            $classGroups = Classes::find($input['class_id'])->classGroups()->get();
            return $this->sendResponse($classGroups, "Class groups fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

}
