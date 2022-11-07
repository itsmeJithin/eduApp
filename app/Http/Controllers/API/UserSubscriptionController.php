<?php


namespace App\Http\Controllers\API;


use App\Constants\PaymentConstants;
use App\Constants\StatusConstants;
use App\Constants\UserType;
use App\Exceptions\CoreException;
use App\Models\ClassGroupSyllabuses;
use App\Models\CourseAnnualFees;
use App\Models\SyllabusSubscriptionMonths;
use App\Models\UserPaymentDetails;
use App\Models\UserSubscribedSyllabusMonths;
use App\Rules\UUID;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;

/**
 *
 * @Date 29/05/21
 */
class UserSubscriptionController extends BaseController
{

    /**
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function getCourseAnnualFee(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class_group_syllabus_id' => ['required', new UUID()],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        try {
            $annualFee = $this->calculateAnnualFee($input['class_group_syllabus_id']);
            return $this->sendResponse($annualFee, "Annual fee details fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param $classGroupSyllabusId
     * @param $courseAnnualFeeId
     * @return null|mixed
     * @throws \Exception
     */
    protected function calculateAnnualFee($classGroupSyllabusId, $courseAnnualFeeId = null)
    {
        $annualFee = CourseAnnualFees::where("class_group_syllabus_id", $classGroupSyllabusId);
        if ($courseAnnualFeeId) {
            $annualFee = $annualFee->where("course_annual_fee_id", $courseAnnualFeeId);
        }
        $annualFee = $annualFee->first();
        if ($annualFee && !$annualFee->price && $annualFee->discount) {
            $totalFee = $this->getTotalSyllabusSubscriptionMonthFee($classGroupSyllabusId);
            if ($totalFee === null)
                throw new \Exception("Course fee not configured.Contact our support team for more information", CoreException::COURSE_FEE_NOT_CONFIGURED);
            $annualFee->price = round($totalFee * ($annualFee->discount / 100), 2);
            return $annualFee;
        } elseif (!$annualFee) {
            return null;
        } else {
            return $annualFee;
        }
    }

    public function checkUserIsSubscribed(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class_group_syllabus_id' => new UUID(),
            'subscription_month_id' => new UUID()
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        try {
            $subscriptionMonthId = $input['subscription_month_id'];
            $classGroupSyllabusId = $input['class_group_syllabus_id'];
            $userId = $request->user()->user_id;
            $syllabusSubscriptionMonthId = SyllabusSubscriptionMonths::select("syllabus_subscription_month_id")
                ->where("class_group_syllabus_id", "=", $classGroupSyllabusId)
                ->where("subscription_month_id", "=", $subscriptionMonthId)
                ->value("syllabus_subscription_month_id");
            if (!empty($syllabusSubscriptionMonthId)) {
                $response = UserSubscribedSyllabusMonths::select("us_syllabus_month_id")
                    ->where("syllabus_subscription_month_id", "=", $syllabusSubscriptionMonthId)
                    ->where("class_group_syllabus_id", "=", $classGroupSyllabusId)
                    ->where("user_id", "=", $userId)
                    ->value("us_syllabus_month_id");
                return $this->sendResponse(!empty($response) ? $response : false, "Subscription status fetched successfully");
            }
            return false;
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function checkout(Request $request)
    {
        $user = $request->user();
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            "is_annual_payment" => "required|boolean",
            "course_annual_fee_id" => "nullable|int",
            "syllabus_subscription_month_id" => "nullable|int",
            "class_group_syllabus_id" => ["required", new UUID()]
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors, CoreException::VALIDATION_ERRORS);
        }
        try {
            $isAnnualPayment = $input['is_annual_payment'];
            $courseAnnualFeeId = $input['course_annual_fee_id'];
            $syllabusSubscriptionMonthId = $input['syllabus_subscription_month_id'];
            $receiptNumber = UserPaymentDetails::max('receipt_no');
            $receiptNumber += 1;
            if ($isAnnualPayment) {
                $subscribedMonths = $this->getCourseSubscribedMonths($user->user_id, $input['class_group_syllabus_id']);
                if ($subscribedMonths && $subscribedMonths->count()) {
                    return $this->sendJsonError("You have already subscribed " . $subscribedMonths->count() . " months. You can not use this option");
                }
                $courseAnnualFee = $this->calculateAnnualFee($input['class_group_syllabus_id'], $courseAnnualFeeId);
                if (!$courseAnnualFee) {
                    return $this->sendJsonError("Course fee details not found");
                }
                $totalAmount = $courseAnnualFee->price;
                $totalAmountParts = explode(".", $totalAmount);
                if (count($totalAmountParts) === 1) {
                    $totalAmount = $totalAmount . ".00";
                } elseif (strlen($totalAmountParts[1]) === 1) {
                    $totalAmount = $totalAmount . "0";
                }
                $api = new Api(env('RAZORPAY_API_KEY'), env('RAZORPAY_SECRET_KEY'));
                $order = $api->order->create(array('receipt' => $receiptNumber,
                        'amount' => str_replace(".", "", $totalAmount),
                        'currency' => 'INR')
                );
                $orderId = $order['id'];
                $purchase = UserPaymentDetails::create([
                    "user_payment_details_id" => \Ramsey\Uuid\Uuid::uuid4(),
                    'user_id' => $user->user_id,
                    "class_group_syllabus_id" => $input['class_group_syllabus_id'],
                    'order_id' => $orderId,
                    'receipt_no' => $receiptNumber,
                    'status' => StatusConstants::PENDING,
                    "amount" => $totalAmount,
                    "course_annual_fee_id" => $courseAnnualFee->course_annual_fee_id
                ]);
                return $this->sendResponse([
                    'transaction_id' => $purchase->user_payment_details_id,
                    'order_id' => $orderId,
                    'amount' => str_replace(".", "", $totalAmount)
                ], "Order created successfully");
            } elseif (!empty($syllabusSubscriptionMonthId)) {

                $syllabusSubscriptionMonth = SyllabusSubscriptionMonths::find($syllabusSubscriptionMonthId);
                if (!$syllabusSubscriptionMonth) {
                    return $this->sendJsonError("Invalid subscription month selection");
                }
                $totalAmount = $syllabusSubscriptionMonth->price;
                $totalAmountParts = explode(".", $totalAmount);
                if (count($totalAmountParts) === 1) {
                    $totalAmount = $totalAmount . ".00";
                } elseif (strlen($totalAmountParts[1]) === 1) {
                    $totalAmount = $totalAmount . "0";
                }
                $api = new Api(env('RAZORPAY_API_KEY'), env('RAZORPAY_SECRET_KEY'));
                $order = $api->order->create(array('receipt' => $receiptNumber,
                        'amount' => str_replace(".", "", $totalAmount),
                        'currency' => 'INR')
                );
                $orderId = $order['id'];
                $purchase = UserPaymentDetails::create([
                    "user_payment_details_id" => \Ramsey\Uuid\Uuid::uuid4(),
                    "class_group_syllabus_id" => $input['class_group_syllabus_id'],
                    'user_id' => $user->user_id,
                    'order_id' => $orderId,
                    'receipt_no' => $receiptNumber,
                    'status' => StatusConstants::PENDING,
                    "amount" => $totalAmount,
                    "syllabus_subscription_month_id" => $syllabusSubscriptionMonth->syllabus_subscription_month_id
                ]);
                return $this->sendResponse([
                    'transaction_id' => $purchase->user_payment_details_id,
                    'order_id' => $orderId,
                    'amount' => str_replace(".", "", $totalAmount)
                ], "Order created successfully");
            } else {
                return $this->sendJsonError("Invalid parameters passes", [], CoreException::VALIDATION_ERRORS);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function completeOrder(Request $request)
    {
        $userId = $request->user()->user_id;
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid parameters passed.', $errors);
        }
        $transactionId = $request->get('transaction_id');
        $paymentId = $request->get('razorpay_payment_id');
        $razorPaySignature = $request->get('razorpay_signature');
        try {
            $userPurchaseDetails = UserPaymentDetails::find($transactionId);
            if (!$userPurchaseDetails) {
                return $this->sendJsonError('Invalid transaction', ["INVALID_TRANSACTION" => 'Invalid transaction']);
            }
            if ($userPurchaseDetails->status !== StatusConstants::PENDING) {
                return $this->sendJsonError("Transaction Already processed", ["TRANSACTION_ALREADY_PROCESSED" => 'Transaction already processes']);
            }
            $signature = hash_hmac("sha256", $userPurchaseDetails->order_id . '|' . $paymentId, env('RAZORPAY_SECRET_KEY'));
            if ($signature !== $razorPaySignature) {
                return $this->sendJsonError('Invalid transaction', ["SIGNATURE_VERIFICATION_FAILED" => 'Invalid transaction signature']);
            }
            $userPurchaseDetails->status = StatusConstants::SUCCESS;
            $responseObject = new \stdClass();
            $responseObject->transaction_id = $transactionId;
            $responseObject->razorpay_payment_id = $paymentId;
            $responseObject->razorpay_order_id = $request->get('razorpay_order_id');
            $responseObject->razorpay_signature = $razorPaySignature;
            $responseObject = json_encode($responseObject);
            $userPurchaseDetails->payment_gateway_response = $responseObject;
            $userPurchaseDetails->save();

            if ($userPurchaseDetails->is_annual_fee_payment) {
                $syllabusSubscriptionMonths = ClassGroupSyllabuses::find($userPurchaseDetails->class_group_syllabus_id)
                    ->syllabusSubscriptionMonths()->get();
                foreach ($syllabusSubscriptionMonths as $subscriptionMonth) {
                    UserSubscribedSyllabusMonths::create([
                        "user_id" => $userId,
                        "class_group_syllabus_id" => $userPurchaseDetails->class_group_syllabus_id,
                        "syllabus_subscription_month_id" => $subscriptionMonth->syllabus_subscription_month_id,
                        "is_active" => $userPurchaseDetails->syllabus_subscription_month_id,
                        "paid_amount" => $userPurchaseDetails->amount,
                        "paid_through" => PaymentConstants::ONLINE,
                        "paid_on" => Carbon::now(),
                        "payment_details_id" => $userPurchaseDetails->payment_details_id,
                        "paid_by" => $userId,
                        "paid_by_user_type" => UserType::STUDENT_USER,
                        "created_by" => $userId,
                        "update_by" => $userId
                    ]);
                }
            } else {
                UserSubscribedSyllabusMonths::create([
                    "user_id" => $userId,
                    "class_group_syllabus_id" => $userPurchaseDetails->class_group_syllabus_id,
                    "syllabus_subscription_month_id" => $userPurchaseDetails->syllabus_subscription_month_id,
                    "is_active" => $userPurchaseDetails->syllabus_subscription_month_id,
                    "paid_amount" => $userPurchaseDetails->amount,
                    "paid_through" => PaymentConstants::ONLINE,
                    "paid_on" => Carbon::now(),
                    "payment_details_id" => $userPurchaseDetails->payment_details_id,
                    "paid_by" => $userId,
                    "paid_by_user_type" => UserType::STUDENT_USER,
                    "created_by" => $userId,
                    "update_by" => $userId
                ]);
            }
            return $this->sendResponse([], "Transaction completed successfully");
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    /**
     * @param $userId
     * @param $classGroupSyllabusId
     * @return mixed
     * @throws \Exception
     */
    protected function getCourseSubscribedMonths($userId, $classGroupSyllabusId)
    {
        try {
            return SyllabusSubscriptionMonths::where("user_id", "=", $userId)
                ->where("class_group_syllabus_id", "=", $classGroupSyllabusId)
                ->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $classGroupSyllabusId
     * @return mixed
     */
    protected function getTotalSyllabusSubscriptionMonthFee($classGroupSyllabusId)
    {
        return SyllabusSubscriptionMonths::where("class_group_syllabus_id", "=", $classGroupSyllabusId)
            ->sum("price");

    }


}
