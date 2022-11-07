<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @Date 30/05/21
 */
class AddonsStudyMaterials extends Model
{
    /**
     * @var string
     */
    protected $table = "addons_study_materials";

    /**
     * @var string
     */
    protected $primaryKey = "asm_id";

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        "asm_id",
        "asm_name",
        "asm_description",
        "asm_url",
        "updated_by",
        "created_by"
    ];

}
