<?php


namespace App\Http\Controllers\API;

use App\Models\LatestNews;
use Illuminate\Http\Request;

/**
 *
 * @Date 01/06/21
 */
class LatestNewsController extends BaseController
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getAllLatestNews(Request $request)
    {
        try {
            $news = LatestNews::where("is_active", "=", 1)
                ->orderBy("created_at")
                ->paginate(10);
            return $this->sendResponse($news, "latest news fetches successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

}
