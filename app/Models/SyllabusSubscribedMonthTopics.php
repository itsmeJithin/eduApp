<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 25/03/21
 */
class SyllabusSubscribedMonthTopics extends Model
{
    use HasApiTokens;

    /**
     * @var string
     */
    protected $table = "ss_month_topics";

    /**
     * @var string
     */
    protected $primaryKey = "ss_month_topic_id";

    /**
     * @var array
     */
    protected $fillable = [
        'topic_id',
        'syllabus_subscription_month_id',
        'priority',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * @return HasMany
     */
    public function topics()
    {
        return $this->hasMany(Topics::class, 'topic_id');
    }

    public function syllabusSubscriptionMonths()
    {
        return $this->hasMany(SyllabusSubscriptionMonths::class, "syllabus_subscription_month_id");
    }

    /**
     * @return HasManyThrough
     */
    public function chapters()
    {
        return $this->hasManyThrough(
            Chapters::class,
            Topics::class,
            "topic_id",
            "chapter_id",
            "topic_id",
            "chapter_id"
        );
    }
}
