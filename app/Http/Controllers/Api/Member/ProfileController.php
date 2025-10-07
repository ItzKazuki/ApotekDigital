<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\Member;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $member = Member::find($request->user()->id);
        return new ProfileResource($member);
    }
}
