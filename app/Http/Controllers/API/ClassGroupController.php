<?php


namespace App\Http\Controllers\API;

use App\Models\ClassGroups;
use App\Rules\UUID;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

/**
 *
 * @Date 07/08/20
 */
class ClassGroupController extends BaseController
{
    public function getAllClassGroups(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, ['class_id' => new UUID()]);
        if ($validator->fails()) {
            return $this->sendJsonError('Invalid class details given', []);
        }
        $classId = $input['class_id'];
        $groups = ClassGroups::where('is_active', 1)->where('class_id', '=', $classId)->get();
        return $this->sendResponse($groups, 'Course groups retrieved successfully.');
    }
}
