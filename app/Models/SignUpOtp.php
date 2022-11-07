<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Softon\Sms\Facades\Sms;

/**
 *
 * @Date 02/08/20
 */
class SignUpOtp extends Model
{
    const EXPIRATION_TIME = 5;

    protected $primaryKey = 'sign_up_otp_id';

    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['otp'])) {
            $attributes['otp'] = $this->generateCode();
        }

        parent::__construct($attributes);
    }

    public function generateCode($codeLength = 4)
    {
        $min = pow(10, $codeLength);
        $max = $min * 10 - 1;
        $code = mt_rand($min, $max);

        return $code;
    }

    protected $fillable = [
        'user_id', 'otp', 'expires_at', 'used'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }

    public function isValid()
    {
        return !$this->isUsed() && !$this->isExpired();
    }

    public function isUsed()
    {
        return $this->used;
    }

    public function isExpired()
    {
        return $this->created_at->diffInMinutes(Carbon::now()) > static::EXPIRATION_TIME;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function sendCode()
    {
        if (!$this->user) {
            throw new \Exception("No user attached to this token.");
        }
        if (!$this->otp) {
            $this->otp = $this->generateCode();
        }
        try {
            Sms::send($this->user->getPhoneNumber(), "Your verification code is {$this->otp}");
        } catch (\Exception $ex) {
            return false; //enable to send SMS
        }

        return true;
    }
}
