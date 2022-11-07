<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 28/05/21
 */
class StudyMaterials extends Model
{
    use HasApiTokens;

    /**
     * @var string
     */
    public $table = "study_materials";

    /**
     * @var string
     */
    protected $primaryKey = "study_material_id";

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        "study_material_id",
        "study_material_name",
        "study_material_description",
        "topic_id",
        'resource_id',
        "study_material_url",
        "created_by"
    ];

    /**
     * @return BelongsTo
     */
    public function topic()
    {
        return $this->belongsTo(Topics::class, 'topic_id');
    }


}
