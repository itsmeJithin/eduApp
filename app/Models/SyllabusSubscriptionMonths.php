<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 12/08/20
 */
class SyllabusSubscriptionMonths extends Model
{
    use HasApiTokens;

    public $table = "syllabus_subscription_months";

    protected $primaryKey = 'syllabus_subscription_month_id';

    protected $fillable = [
        'syllabus_subscription_month_id',
        'class_group_syllabus_id',
        'subscription_month_id',
        'price',
        'priority',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];




}
