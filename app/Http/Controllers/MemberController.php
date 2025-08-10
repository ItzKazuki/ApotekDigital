<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $members = $query->paginate(10);
        return view('admin.member.index', compact('members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15|unique:members,phone',
        ]);

        Member::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'expires_at' => now()->addMonth(),
            'point' => $request->point ?? 0,
        ]);

        return redirect()->route('admin.member.index')->with('success', 'Member created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15|unique:members,phone,' . $member->id,
            'expires_at' => 'nullable|date',
            'point' => 'nullable|numeric',
        ]);

        $member->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'expires_at' => $request->expires_at ? now()->parse($request->expires_at) : $member->expires_at,
            'point' => $request->point ?? $member->point,
        ]);

        return redirect()->route('admin.member.index')->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('admin.member.index')->with('success', 'Member deleted successfully.');
    }
}
