<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @Date 09/05/21
 */
class QuestionTypes extends Model
{
    protected $table = "question_types";

    protected $primaryKey = "question_type_id";

    protected $fillable = [
        "question_type_name",
        "question_type_description",
        "created_by",
        "updated_by"
    ];

}
