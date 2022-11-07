<?php


namespace App\Http\Controllers\API;

use App\Models\SubscriptionMonths;
use App\Models\SyllabusSubscriptionMonths;
use App\Models\UserSubscribedSyllabusMonths;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Rules\UUID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 *
 * @Date 09/08/20
 */
class SubscriptionMonthController extends BaseController
{
    public function getAllSubscriptionMonths(Request $request)
    {
        $queryParams = $request->all();
        $validator = Validator::make($queryParams, [
            "class_group_syllabus_id" => new UUID(),
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        try {
            $classGroupSyllabusId = $queryParams['class_group_syllabus_id'];
            $userId = $request->user()->user_id;
            $subscriptionMonths = SubscriptionMonths::where('is_active', 1)
                ->orderBy('subscription_month_order')
                ->get();
            foreach ($subscriptionMonths as $month) {
                $subscriptionId = DB::table('user_subscribed_syllabus_months as ussm')
                    ->join("syllabus_subscription_months as ssm", function ($join) {
                        $join->on("ssm.syllabus_subscription_month_id", "=", "ussm.syllabus_subscription_month_id")
                            ->on("ussm.class_group_syllabus_id", "=", "ussm.class_group_syllabus_id");
                    })
                    ->join("subscription_months as sm", "sm.subscription_month_id", "=", "ssm.subscription_month_id")
                    ->where("ussm.user_id", "=", $userId)
                    ->where("ussm.class_group_syllabus_id", "=", $classGroupSyllabusId)
                    ->where("sm.subscription_month_id", "=", $month->subscription_month_id)
                    ->select("ussm.us_syllabus_month_id")
                    ->value("ussm.us_syllabus_month_id");
                if ($subscriptionId) {
                    $month->isSubscribed = true;
                    $month->user_subscription_id = $subscriptionId;
                } else {
                    $month->isSubscribed = false;
                    $month->user_subscription_id = null;
                }
            }
            return $this->sendResponse($subscriptionMonths, "Subscription months retrieved successfully");
        } catch (Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function getSubscriptionMonthPrice(Request $request)
    {
        $queryParams = $request->all();
        $validator = Validator::make($queryParams, [
            "class_group_syllabus_id" => [new UUID(), 'required'],
            "subscription_month_id" => [new UUID(), "required"]
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        try {
            $subscriptionMonth = SyllabusSubscriptionMonths::where("class_group_syllabus_id", "=", $queryParams['class_group_syllabus_id'])
                ->where("subscription_month_id", "=", $queryParams['subscription_month_id'])
                ->first();
            return $this->sendResponse($subscriptionMonth, "Subscription month fetched successfully");
        } catch (Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }
}
