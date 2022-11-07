<?php


namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 *
 * @Date 09/04/21
 */
class StaffUsers extends Authenticatable
{
    use SoftDeletes, Notifiable;

    /**
     * @var string
     */
    protected $primaryKey = 'staff_user_id';

    /**
     * @var string
     */
    protected $table = "staff_users";

    /**
     * @var bool
     */
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_user_id',
        'staff_name',
        'staff_code',
        'staff_email',
        'staff_password',
        'staff_phone_number',
        'nationality',
        'state',
        'address',
        'pin_code',
        'is_active',
        'activation_token',
        'otp_verified_at',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'activation_token'
    ];


    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->staff_password;
    }

    public function getEmailForVerification()
    {
        return $this->staff_email;
    }

    public function getEmailForPasswordReset()
    {
        return $this->staff_email;
    }


}
