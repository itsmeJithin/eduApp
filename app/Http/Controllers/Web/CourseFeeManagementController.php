<?php

namespace App\Http\Controllers\Web;

use App\Models\ClassGroupSyllabuses;
use App\Models\CourseAnnualFees;
use App\Models\SyllabusSubscriptionMonths;
use App\Rules\UUID;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @Date 10/06/21
 */
class CourseFeeManagementController extends StaffBaseController
{
    /**
     * @return Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        try {
            return view("pages.courseFee.courseFee");
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAssignedMonthFee(Request $request)
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
            $classGroupSyllabus = ClassGroupSyllabuses::where("class_group_syllabus_id", "=", $input['class_group_syllabus_id'])
                ->first();
            if ($classGroupSyllabus) {
                $classGroupSyllabus->subscription_months = $classGroupSyllabus->subscriptionMonths()->get();
                foreach ($classGroupSyllabus->subscription_months as $month) {
                    $ssMonth = SyllabusSubscriptionMonths::where("class_group_syllabus_id", "=", $classGroupSyllabus->class_group_syllabus_id)
                        ->where("subscription_month_id", "=", $month->subscription_month_id)
                        ->first();
                    $month->price = $ssMonth->price;
                }
            }
            return $this->sendResponse($classGroupSyllabus, "Subscription months fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function saveMonthFee(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            "class_group_syllabus_id" => ["required", new UUID()],
            'subscription_month_id' => ["required", new UUID()],
            "price" => 'numeric'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        try {
            $userId = $request->user()->staff_user_id;
            $month = SyllabusSubscriptionMonths::where("class_group_syllabus_id", "=", $input['class_group_syllabus_id'])
                ->where("subscription_month_id", "=", $input['subscription_month_id'])
                ->first();
            $month->price = $input['price'];
            $month->updated_by = $userId;
            $month->save();
            return $this->sendResponse(null, "Price updated successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAnnualFee(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            "class_group_syllabus_id" => ["required", new UUID()],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        try {
            $classGroupSyllabus = ClassGroupSyllabuses::find($input['class_group_syllabus_id']);
            if ($classGroupSyllabus) {
                $totalAssignedFee = $classGroupSyllabus->subscriptionMonths()->sum("price");
                $annualFee = $classGroupSyllabus->annualFee()->first();
                return $this->sendResponse([
                    "annualFee" => $annualFee,
                    "totalAssignedFee" => $totalAssignedFee
                ], "Annual fee details fetched successfully");
            } else {
                return $this->sendJsonError("Invalid class group details given");
            }
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function saveAnnualFee(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            "class_group_syllabus_id" => ["required", new UUID()],
            "price" => "nullable|numeric",
            "discount" => "nullable|numeric"
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent.', $errors);
        }
        try {
            $discount = $input['discount'];
            $userId = $request->user()->staff_user_id;
            $price = $input['price'];
            if (!empty($discount) && !empty($price)) {
                return $this->sendJsonError("You should not enter both fee and discount");
            }
            $classGroupSyllabus = ClassGroupSyllabuses::find($input['class_group_syllabus_id']);
            if ($classGroupSyllabus) {
                $annualFee = $classGroupSyllabus->annualFee()->first();
                if ($annualFee) {
                    $annualFee->discount = $input['discount'];
                    $annualFee->price = $input['price'];
                    $annualFee->updated_by = $userId;
                    $annualFee->save();
                } else {
                    CourseAnnualFees::create([
                        "class_group_syllabus_id" => $input['class_group_syllabus_id'],
                        "price" => $price,
                        "discount" => $discount,
                        "created_by" => $userId
                    ]);
                }
                return $this->sendResponse(null, "Annual fee details updated successfully");
            } else {
                return $this->sendJsonError("Invalid class group details given");
            }
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }


}
