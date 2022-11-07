<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 * @Date 28/05/21
 */
class TopicDoubts extends Model
{

    /**
     * @var string
     */
    protected $table = "topic_doubts";

    /**
     * @var string
     */
    protected $primaryKey = "topic_doubt_id";

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        "topic_doubt_id",
        "doubt",
        "topic_id",
        "created_by",
        "updated_by"
    ];

    /**
     * @return HasMany
     */
    public function doubtAnswers()
    {
        return $this->hasMany(TopicDoubtAnswers::class, "topic_doubt_id");
    }

}
