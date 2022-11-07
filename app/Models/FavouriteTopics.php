<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 30/05/21
 */
class FavouriteTopics extends Model
{
    use HasApiTokens;
    /**
     * @var string
     */
    protected $table = "favourite_topics";

    /**
     * @var string
     */
    protected $primaryKey = "favourite_topic_id";

    /**
     * @var array
     */
    protected $fillable = [
        "user_id",
        "subscription_month_id",
        "class_group_syllabus_id",
        "topic_id",
        "created_by"
    ];

    /**
     * @return HasOne
     */
    public function topic()
    {
        return $this->hasOne(Topics::class, "topic_id","topic_id");
    }

}
