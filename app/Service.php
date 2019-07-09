<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    protected $table = 'services';
    protected $fillable = [
        'name'
    ];


    public function users(){
        return $this->belongsToMany('App\User');
    }
}
