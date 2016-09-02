<?php

namespace App;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    public $fillable = ['user1', 'user2'];

    public function flist() {
        return $this->hasOne('App\User', 'id', 'user2');
    }
}
