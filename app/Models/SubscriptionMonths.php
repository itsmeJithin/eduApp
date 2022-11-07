<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 09/08/20
 */
class SubscriptionMonths extends Model
{
    use HasApiTokens;

    protected $primaryKey = 'subscription_month_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'subscription_month_id',
        'subscription_month_name',
        'subscription_month_code',
        'subscription_month_days',
        'subscription_month_order',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
