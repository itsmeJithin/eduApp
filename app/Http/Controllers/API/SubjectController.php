<?php


namespace App\Http\Controllers\API;

use App\Models\ClassGroupSyllabuses;
use App\Models\ClassGroupSyllabusSubjects;
use App\Rules\UUID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 *
 * @Date 08/08/20
 */
class SubjectController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAllSubjects(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['class_group_syllabus_id' => new UUID()]);
        if ($validator->fails()) {
            return $this->sendJsonError('Invalid parameters given', []);
        }
        $classGroupSyllabusId = $input['class_group_syllabus_id'];
        $subjects = ClassGroupSyllabuses::find($classGroupSyllabusId)->subjects()->get();
        foreach ($subjects as $subject) {
            $id = ClassGroupSyllabusSubjects::select("class_group_syllabus_subject_id")
                ->where("class_group_syllabus_id", "=", $classGroupSyllabusId)
                ->where("subject_id", "=", $subject->subject_id)
                ->value("class_group_syllabus_subject_id");
            $subject->class_group_syllabus_subject_id = $id;
        }
        return $this->sendResponse($subjects, 'Subjects retrieved successfully.');

    }

}
