<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @Date 28/05/21
 */
class TopicDoubtAnswers extends Model
{
    /**
     * @var string
     */
    protected $table = "topic_doubt_answers";

    /**
     * @var string
     */
    protected $primaryKey = "topic_doubt_answer_id";

    /**
     * @var array
     */
    protected $fillable = [
        "topic_doubt_id",
        "answer",
        "answered_by",
        "updated_by"
    ];

    /**
     * @return BelongsTo
     */
    public function doubt()
    {
        return $this->belongsTo(TopicDoubts::class, "topic_doubt_id");
    }
}
