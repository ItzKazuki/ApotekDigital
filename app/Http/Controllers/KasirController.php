<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('transactions.transactionDetails.drug')->where('role', 'kasir');

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'online');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $kasir = $query->paginate(10);

        return view('admin.kasir.index', compact('kasir'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20|unique:users,phone',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'kasir',
            'phone' => $request->phone,
            'is_logged_in' => false,
        ]);

        return redirect()->route('admin.kasir.index')->with('success', 'Kasir created successfully.');
    }

    public function update(Request $request, $id)
    {
        $kasir = User::where('role', 'kasir')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $kasir->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $id,
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $kasir->update($data);

        return redirect()->route('admin.kasir.index')->with('success', 'Kasir updated successfully.');
    }

    public function destroy($id)
    {
        $kasir = User::where('role', 'kasir')->findOrFail($id);
        $kasir->delete();
        return redirect()->route('admin.kasir.index')->with('success', 'Kasir deleted successfully.');
    }
}
