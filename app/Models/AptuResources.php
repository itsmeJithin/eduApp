<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @Date 07/06/21
 */
class AptuResources extends Model
{
    /**
     * @var string
     */
    protected $table = "aptu_resources";

    /**
     * @var string
     */
    protected $primaryKey = "aptu_resource_id";

    protected $fillable = [
        "aptu_resource_id",
        "file_name",
        "storage_object",
        "file_url",
        "storage_type",
        "created_by",
        "updated_by"
    ];


}
