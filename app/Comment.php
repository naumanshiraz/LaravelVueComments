<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    /**
     * Fillable fields for a course
     *
     * @return array
     */
    protected $fillable = ['comment','reply_id','username'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function replies()
    {
        return $this->hasMany('App\Comment','id','reply_id');
    }
}