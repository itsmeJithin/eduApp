<?php


namespace App\Http\Controllers\API;

use App\Models\AddonsStudyMaterials;
use App\Models\AddonsVideoMaterials;
use App\Models\StudyMaterials;
use Illuminate\Http\Request;


/**
 *
 * @Date 30/05/21
 */
class AddonsController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getAllAddonsStudyMaterials(Request $request)
    {
        try {
            $studyMaterials = AddonsStudyMaterials::paginate(15);
            return $this->sendResponse($studyMaterials, "Study materials fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getAllAddonsVideoMaterials(Request $request)
    {
        try {
            $studyMaterials = AddonsVideoMaterials::paginate(25);
            return $this->sendResponse($studyMaterials, "Video materials fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }

    }

}
