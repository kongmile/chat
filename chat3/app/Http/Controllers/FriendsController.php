<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Friend;
use App\Http\Requests;

class FriendsController extends Controller
{
    public function friendList() {
        $friends = Friend::where('user1', Auth::user()->id)->has
    }
}
