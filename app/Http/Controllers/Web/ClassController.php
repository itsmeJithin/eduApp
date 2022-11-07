<?php


namespace App\Http\Controllers\Web;

use App\Exceptions\CoreException;
use App\Models\Classes;
use App\Models\Courses;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

/**
 *
 * @Date 03/06/21
 */
class ClassController extends StaffBaseController
{
    public function index()
    {
        return view("pages.classes.classes");
    }

    /**
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAllClasses()
    {
        try {
            $classes = Classes::with("course")
                ->where("is_active", "=", "1")
                ->orderBy("updated_at", "DESC")
                ->orderBy("created_at", "DESC")
                ->get();
            return $this->sendResponse($classes, "Classes fetched successfully");
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
            $class = null;
            if (isset($input['class_id']) && !empty($input['class_id'])) {
                $validator = Validator::make($request->all(), [
                    'class_name' => 'required',
                    'class_code' => 'required',
                    'course_id' => 'required|int'
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $class = Classes::find($input['class_id']);
                if (!$class) {
                    return $this->sendJsonError("Course not found", [], CoreException::COURSE_NOT_FOUND);
                }
                $isCourseCodeExist = Classes::where("class_code", "=", $input['class_code'])
                    ->where("class_id", "!=", $class->class_id)->first();
                if ($isCourseCodeExist) {
                    return $this->sendJsonError("Duplicate course code", [], CoreException::DUPLICATE_CLASS_CODE);
                }
                $class->class_name = $input['class_name'];
                $class->class_code = $input['class_code'];
                $class->course_id = $input['course_id'];
                $class->class_description = $input['class_description'];
                $class->updated_by = $userId;
                $class->save();
                return $this->sendResponse($class, "Class updated successfully");
            } else {
                $validator = Validator::make($request->all(), [
                    'class_name' => 'required|min:3',
                    'class_code' => 'required|min:3|unique:classes,class_code',
                    'course_id' => 'required|int',
                ]);
                $input['created_by'] = $userId;
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $input['class_id'] = Uuid::uuid4();
                $class = Classes::create($input);
                return $this->sendResponse($class, "Class created successfully");
            }

        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function deleteClass(Request $request)
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
            $class = Classes::find($input['class_id']);
            $class->delete();
            return $this->sendResponse(null, "Class deleted successfully");
        } catch (\Exception $e) {
            if ($e->getCode() === CoreException::INTEGRITY_CONSTRAINT_ERROR) {
                return $this->sendJsonError("You can't delete this class because this class contains class groups. Remove all those class groups before trying again", [], CoreException::INTEGRITY_CONSTRAINT_ERROR);
            }
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getClassesByCourse(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'course_id' => "required|int"
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Validation Error.', $errors);
            }
            $classes = Courses::find($input['course_id'])->classes()->get();
            return $this->sendResponse($classes, "Classes fetched successfully");

        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }

    }

}
