<?php

use App\Models\StaffUsers;
use App\Models\SyllabusSubscriptionMonths;
use App\Models\UserSubscribedSyllabusMonths;


/**
 * @param $classGroupSyllabusId
 * @param $subscriptionMonthId
 * @param $userId
 * @return bool|string
 */
function checkUserSubscribed($classGroupSyllabusId, $subscriptionMonthId, $userId)
{
    $syllabusSubscriptionMonthId = SyllabusSubscriptionMonths::select("syllabus_subscription_month_id")
        ->where("class_group_syllabus_id", "=", $classGroupSyllabusId)
        ->where("subscription_month_id", "=", $subscriptionMonthId)
        ->value("syllabus_subscription_month_id");
    if (!empty($syllabusSubscriptionMonthId)) {
        return UserSubscribedSyllabusMonths::select("us_syllabus_month_id")
            ->where("syllabus_subscription_month_id", "=", $syllabusSubscriptionMonthId)
            ->where("class_group_syllabus_id", "=", $classGroupSyllabusId)
            ->where("user_id", "=", $userId)
            ->value("us_syllabus_month_id");
    }
    return false;
}

/**
 * @param $length
 * @return false|string
 */
function generateUniqueStaffCode($length)
{
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // Shuffle the $str_result and returns substring
    // of specified length
    $code = substr(str_shuffle($str_result),
        0, $length);
    $staff = StaffUsers::where("staff_code", "=", $code)->first();
    if ($staff)
        generateUniqueStaffCode(4);
    return $code;
}

/**
 * @param $classGroupSyllabusId
 * @param $subscriptionMonthId
 * @return mixed
 * @throws Exception
 */
function getSyllabusSubscriptionId($classGroupSyllabusId, $subscriptionMonthId)
{
    try {
        return SyllabusSubscriptionMonths::where("class_group_syllabus_id", "=", $classGroupSyllabusId)
            ->where("subscription_month_id", "=", $subscriptionMonthId)
            ->first();
    } catch (\Exception $e) {
        throw $e;
    }
}
