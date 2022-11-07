<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @Date 06/07/21
 */
class UserPaymentDetails extends Model
{
    /**
     * @var string
     */
    protected $table = "user_payment_details";

    /**
     * @var string
     */
    protected $primaryKey = "user_payment_details_id";

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        "receipt_no",
        "user_id",
        "order_id",
        "amount",
        "status",
        "payment_gateway_response",
        "is_annual_fee_payment",
        "course_annual_fee_id",
        "syllabus_subscription_month_id",
        "created_at",
        "updated_at"
    ];

}
