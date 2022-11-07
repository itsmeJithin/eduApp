<?php


namespace App\Http\Controllers\API;

use App\Models\Courses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 *
 * @Date 07/08/20
 */
class CourseController extends BaseController
{
    public function createCourse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'course_name' => 'required|unique:courses',
            'course_code' => 'required|unique:courses',
            'course_description' => 'nullable'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors);
        }
        $input = $request->all();
        $course = Courses::create($input);
        $success['course_id'] = $course->course_id;
        return $this->sendResponse($success, 'Course created successfully.');
    }

}
