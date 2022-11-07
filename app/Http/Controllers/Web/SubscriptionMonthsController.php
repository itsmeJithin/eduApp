<?php


namespace App\Http\Controllers\Web;

use App\Exceptions\CoreException;
use App\Models\ClassGroupSyllabuses;
use App\Models\SubscriptionMonths;
use App\Models\SyllabusSubscriptionMonths;
use App\Rules\UUID;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 *
 * @Date 05/06/21
 */
class SubscriptionMonthsController extends StaffBaseController
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view("pages.subscriptionMonths.subscriptionMonths");
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getClassSubscriptionMonths(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                "class_group_syllabus_id" => ["required", new UUID()]
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Invalid request sent.', $errors);
            }
            $classGroupSyllabus = ClassGroupSyllabuses::with("subscriptionMonths")
                ->where("class_group_syllabus_id", "=", $input['class_group_syllabus_id'])
                ->first();
            $availableMonths = SubscriptionMonths::where("is_active", "=", 1)->get();
            return $this->sendResponse([
                "classGroupSyllabus" => $classGroupSyllabus,
                "availableMonths" => $availableMonths
            ], "Subscription months fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAssignedClassSubscriptionMonths(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                "class_group_syllabus_id" => ["required", new UUID()]
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Invalid request sent.', $errors);
            }
            $classGroupSyllabus = ClassGroupSyllabuses::with("subscriptionMonths")
                ->where("class_group_syllabus_id", "=", $input['class_group_syllabus_id'])
                ->first();
            return $this->sendResponse($classGroupSyllabus, "Subscription months fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function assignClassGroupSyllabusSubscriptionMonths(Request $request)
    {
        $deletingMonth = null;
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                "class_group_syllabus_id" => ["required", new UUID()]
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Invalid request sent.', $errors);
            }
            $assignedMonths = $input['assignedSubscriptionMonths'];
            $assignedMonths = json_decode($assignedMonths);
            $deletedMonths = $input['deletedSubscriptionMonths'];
            $userId = $request->user()->staff_user_id;
            foreach ($deletedMonths as $deletedMonth) {
                $deletingMonth = SyllabusSubscriptionMonths::where("class_group_syllabus_id", "=", $input['class_group_syllabus_id'])
                    ->where("subscription_month_id", "=", $deletedMonth)
                    ->first();
                if ($deletingMonth)
                    $deletingMonth->delete();
            }
            $i = 0;
            foreach ($assignedMonths as $month) {
                ++$i;
                if (isset($month->pivot)) {
                    $subscriptionMonth = SyllabusSubscriptionMonths::where("class_group_syllabus_id", "=", $input['class_group_syllabus_id'])
                        ->where("subscription_month_id", "=", $month->subscription_month_id)
                        ->first();
                    $subscriptionMonth->priority = $i;
                    $subscriptionMonth->updated_by = $userId;
                    $subscriptionMonth->save();
                } else {
                    $subscriptionMonth = SyllabusSubscriptionMonths::create([
                        "class_group_syllabus_id" => $input['class_group_syllabus_id'],
                        "subscription_month_id" => $month->subscription_month_id,
                        "price" => 0,
                        "created_by" => $userId,
                        "priority" => $i
                    ]);

                }
            }
            return $this->sendResponse(null, "Subscription months assigned successfully");
        } catch (\Exception $e) {
            if ($e->getCode() === CoreException::INTEGRITY_CONSTRAINT_ERROR) {
                $month = SubscriptionMonths::find($deletingMonth->subscription_month_id);
                return $this->sendJsonError("You cannot remove month `" . $month->subscription_month_name . "` from the list because this contain subject topics. Remove topics from this month and try again");
            }
            return $this->sendJsonError($e->getMessage());
        }
    }

}
