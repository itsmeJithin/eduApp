<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Bridge\User;

/**
 *
 * @Date 26/06/21
 */
class UsersFCMTokens extends Model
{
    /**
     * @var string
     */
    protected $table = "users_fcm_tokens";
    /**
     * @var string
     */
    protected $primaryKey = "users_fcm_token_id";
    /**
     * @var array
     */
    protected $fillable = [
        "user_id",
        "token",
        "created_at",
        "updated_at"
    ];

    public function user()
    {
        return $this->hasOne(User::class, "user_id", "user_id");
    }

}
