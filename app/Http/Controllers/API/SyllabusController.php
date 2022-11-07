<?php


namespace App\Http\Controllers\API;

use App\Models\ClassGroups;
use App\Models\ClassGroupSyllabuses;
use App\Models\Syllabuses;
use App\Rules\UUID;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 *
 * @Date 11/08/20
 */
class SyllabusController extends BaseController
{
    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function getAllClassGroupSyllabuses(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['class_group_id' => new UUID()]);
        if ($validator->fails()) {
            return $this->sendJsonError('Invalid parameters given', []);
        }
        $classGroupId = $input['class_group_id'];
        $syllabuses = ClassGroups::find($classGroupId)->syllabuses()->get();
        foreach ($syllabuses as $syllabus) {
            $id = ClassGroupSyllabuses::select("class_group_syllabus_id")
                ->where("syllabus_id", "=", $syllabus->syllabus_id)
                ->where("class_group_id", "=", $classGroupId)
                ->value("class_group_syllabus_id");
            $syllabus->class_group_syllabus_id = $id;
        }
        return $this->sendResponse($syllabuses, "Syllabuses retrieved successfully");

    }

}
