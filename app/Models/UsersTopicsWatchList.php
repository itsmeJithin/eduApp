<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 01/06/21
 */
class UsersTopicsWatchList extends Model
{
    use HasApiTokens;

    /**
     * @var string
     */
    protected $table = "users_topics_watch_list";

    /**
     * @var string
     */
    protected $primaryKey = "users_topics_watch_list_id";

    /**
     * @var array
     */
    protected $fillable = [
        "user_id",
        "class_group_syllabus_id",
        "topic_id",
        "watch_time",
        "created_at",
        "updated_at"
    ];

    /**
     * @return BelongsTo
     */
    public function topics()
    {
        return $this->belongsTo(Topics::class, "topic_id");
    }
}
