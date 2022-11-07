<?php


namespace App\Http\Controllers\Web;

use App\Exceptions\CoreException;
use App\Models\Courses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

/**
 *
 * @Date 03/06/21
 */
class CourseController extends StaffBaseController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        try {
            return view("pages.courses.courses");
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAllCourses()
    {
        try {
            $courses = Courses::where("is_active", "=", "1")->get();
            return $this->sendResponse($courses, "Courses fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    public function createOrUpdate(Request $request)
    {
        try {
            $userId = $request->user()->staff_user_id;
            $input = $request->all();
            $course = null;
            if (isset($input['course_id']) && !empty($input['course_id'])) {
                $validator = Validator::make($request->all(), [
                    'course_name' => 'required',
                    'course_code' => 'required',
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $course = Courses::find($input['course_id']);
                if (!$course) {
                    return $this->sendJsonError("Course not found", [], CoreException::COURSE_NOT_FOUND);
                }
                $isCourseCodeExist = Courses::where("course_code", "=", $input['course_code'])
                    ->where("course_id", "!=", $course->course_id)->first();
                if ($isCourseCodeExist) {
                    return $this->sendJsonError("Duplicate course code", [], CoreException::DUPLICATE_COURSE_CODE);
                }
                $course->course_name = $input['course_name'];
                $course->course_code = $input['course_code'];
                $course->course_description = $input['course_description'];
                $course->updated_by = $userId;
                $course->save();
            } else {
                $validator = Validator::make($request->all(), [
                    'course_name' => 'required',
                    'course_code' => 'required|unique:courses,course_code',
                ]);
                $input['created_by'] = $userId;
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $course = Courses::create($input);
            }

            return $this->sendResponse($course, "Course created successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function deleteCourse(Request $request)
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
            $course = Courses::find($input['course_id']);
            $course->delete();
            return $this->sendResponse(null, "Course deleted successfully");
        } catch (\Exception $e) {
            if ($e->getCode() === CoreException::INTEGRITY_CONSTRAINT_ERROR) {
                return $this->sendJsonError("You can't delete this course because this course contains classes. Remove all those classes before trying again", [], CoreException::INTEGRITY_CONSTRAINT_ERROR);
            }
            return $this->sendJsonError($e->getMessage());
        }
    }
}
