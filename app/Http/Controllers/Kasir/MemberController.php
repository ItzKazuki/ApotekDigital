<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        return view('kasir.member');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:members,phone',
        ]);

        $member = Member::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'expires_at' => now()->addMonth(),
            'point' => $request->point ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'member' => $member
        ]);
    }

    /**
     * Search member by phone number.
     */
    public function searchMember(Request $request)
    {
        $member = Member::where('phone', $request->phone)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'member' => $member->makeHidden(['id', 'updated_at']),
            'promo' => config('promo.enabled') ? $member->checkPromoMember() : null
        ]);
    }
}
