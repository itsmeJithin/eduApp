<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Twilio\Rest\Client;

/**
 *
 * @Date 01/07/21
 */
class Parents extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $table = "users";

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
        'mobile_number',
        'parent_number',
        'nationality',
        'state',
        'city',
        'pin_code',
        'role_id',
        'is_active',
        'activation_token',
        'otp_verified_at',
        'avatar',
        'referral_code'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'activation_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return \Storage::url('avatars/' . $this->user_id . '/' . $this->avatar);
    }

    /**
     * @param $username
     * @return mixed
     */
    public function findForPassport($username)
    {
        return $this->where('parent_number', $username)->first();
    }

    /**
     * @return HasMany
     */
    public function tokens()
    {
        return $this->hasMany(SignUpOtp::class);
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->parent_number;
    }

    /**
     * @param $password
     * @return bool
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function validateForPassportPasswordGrant($password)
    {
        $twilio = new Client(config('app.twilio.SID'), config('app.twilio.TOKEN'));
        try {
            $verification = $twilio->verify->v2->services(config('app.twilio.TWILIO_VERIFICATION_SID'))
                ->verificationChecks
                ->create($password, array('to' => '+91' . $this->getPhoneNumber()));
            if ($verification->valid) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    /**
     * @return HasMany
     */
    public function fcmTokens()
    {
        return $this->hasMany(UsersFCMTokens::class, "user_id", "user_id");
    }

    /**
     * @return HasMany
     */
    public function subscriptionMonths()
    {
        return $this->hasMany(UserSubscribedSyllabusMonths::class, "user_id", "user_id");
    }

}
