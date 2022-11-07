<?php


namespace App\Http\Controllers\API;

use App\Models\Classes;
use Illuminate\Http\Request;

/**
 *
 * @Date 07/08/20
 */
class ClassController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getAllClasses(Request $request)
    {
        $classes = Classes::with('course')
            ->get()
            ->where('course.is_active', 1)
            ->where('is_active', 1);

        return $this->sendResponse($classes, 'Classes retrieved successfully.');

    }

}
