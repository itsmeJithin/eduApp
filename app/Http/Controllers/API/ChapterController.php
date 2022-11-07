<?php


namespace App\Http\Controllers\API;

use App\Models\ClassGroupSyllabusSubjects;
use App\Rules\UUID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 *
 * @Date 08/08/20
 */
class ChapterController extends BaseController
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getAllChapters(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['class_group_syllabus_subject_id' => "required"]);
        if ($validator->fails()) {
            return $this->sendJsonError('Invalid subject details given', []);
        }
        $classGroupSyllabusSubjectId = $input['class_group_syllabus_subject_id'];
        $chapters = ClassGroupSyllabusSubjects::find($classGroupSyllabusSubjectId)->chapters()->get();
        return $this->sendResponse($chapters, 'Chapters retrieved successfully.');

    }

}
