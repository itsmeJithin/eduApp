<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @Date 30/05/21
 */
class AddonsVideoMaterials extends Model
{
    /**
     * @var string
     */
    protected $table = "addons_video_materials";

    /**
     * @var string
     */
    protected $primaryKey = "avm_id";

    /**
     * @var string
     */
    public $incrementing = "false";

    /**
     * @var array
     */
    protected $fillable = [
        "avm_id",
        "avm_name",
        "avm_description",
        "avm_url",
        "updated_by",
        "created_by"
    ];

}
