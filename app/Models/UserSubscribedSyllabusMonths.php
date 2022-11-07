<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @Date 13/08/20
 */
class UserSubscribedSyllabusMonths extends Model
{
    /**
     * @var string
     */
    protected $table = 'user_subscribed_syllabus_months';

    /**
     * @var string
     */
    protected $primaryKey = 'us_syllabus_month_id';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'syllabus_subscription_month_id',
        'is_active',
        'in_active_remarks',
        "class_group_syllabus_id",
        "paid_amount",
        "paid_through",
        "paid_on",
        "paid_by",
        "paid_by_user_type",
        "payment_details_id",
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }

}
