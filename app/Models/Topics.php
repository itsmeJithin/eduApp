<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 07/08/20
 */
class Topics extends Model
{
    use HasApiTokens;

    protected $primaryKey = 'topic_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'topic_id',
        'topic_name',
        'topic_code',
        'chapter_id',
        'topic_description',
        'video_url',
        'is_demo_topic',
        'is_published',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * returns all study materials associated with given topic id
     * @return HasMany
     */
    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterials::class, "topic_id");
    }

    /**
     * @return HasMany
     */
    public function doubts()
    {
        return $this->hasMany(TopicDoubts::class, "topic_id");
    }

    /**
     * @return HasOne
     */
    public function favouriteTopic()
    {
        return $this->hasOne(FavouriteTopics::class, "topic_id");
    }

    public function chapter()
    {
        return $this->hasOne(Chapters::class, "chapter_id", "chapter_id");
    }
}
