<?php


namespace App\Http\Controllers\API;

/**
 *
 * @Date 01/07/21
 */
class UserParentController extends BaseController
{
    public function index()
    {
        return $this->sendResponse(true, "success");
    }

}
