<?php


namespace App\Http\Controllers\API;

use App\Rules\UUID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


/**
 *
 * @Date 30/05/21
 */
class LiveClassController extends BaseController
{
    /**
     * returns available live classes
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAllLiveClasses(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            "class_group_syllabus_id" => new UUID(),
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        $userId = $request->user()->user_id;
        $classGroupSyllabusId = $input['class_group_syllabus_id'];
        try {
            $liveClasses = DB::table("live_classes as ls")
                ->join("class_group_syllabus_subjects as cgss", "cgss.class_group_syllabus_subject_id", "=", "ls.class_group_syllabus_subject_id")
                ->join("class_group_syllabuses as cgs", "cgs.class_group_syllabus_id", "=", "cgss.class_group_syllabus_id")
                ->join("syllabus_subscription_months as ssm", function ($join) {
                    $join->on("ssm.class_group_syllabus_id", "=", "cgs.class_group_syllabus_id")
                        ->on("ssm.subscription_month_id", "=", "ls.subscription_month_id");
                })
                ->join("user_subscribed_syllabus_months as ussm", function ($join) use ($userId) {
                    $join->on("ussm.class_group_syllabus_id", "=", "cgs.class_group_syllabus_id")
                        ->on("ussm.syllabus_subscription_month_id", "=", "ssm.syllabus_subscription_month_id");
                })
                ->where("ussm.user_id", "=", $userId)
                ->where("cgs.class_group_syllabus_id", "=", $classGroupSyllabusId)
                ->orderBy("ls.created_at", "DESC")
                ->select("ls.*")
                ->get();
            return $this->sendResponse($liveClasses, "Study materials are fetched successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), "ERROR", 200);
        }
    }

}
