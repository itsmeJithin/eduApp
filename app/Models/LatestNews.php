<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 01/06/21
 */
class LatestNews extends Model
{
    use HasApiTokens;
    /**
     * @var string
     */
    protected $table = "latest_news";

    /**
     * @var string
     */
    protected $primaryKey = "latest_news_id";

    /**
     * @var array
     */
    protected $fillable = [
        "latest_news_title",
        "latest_news_description",
        "latest_news_image_url",
        "created_by",
        "updated_by",
        "created_at",
        "updated_at"
    ];

}
