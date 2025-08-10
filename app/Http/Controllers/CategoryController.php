<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(10);

        return view('admin.category.index', compact('categories'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa string.',
            'name.max' => 'Nama kategori tidak boleh lebih dari 255 karakter.',
        ]);

        // cek apakah kategori sudah ada
        if (Category::where('name', $request->name)->exists()) {
            return redirect()->back()->with(['error' => "Kategori dengan '{$request->name}' ini sudah ada."]);
        }

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'image_path' => $request->file('image') ? $request->file('image')->store('categories', 'public') : null,
        ]);

        return redirect()->route('admin.category.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa string.',
            'name.max' => 'Nama kategori tidak boleh lebih dari 255 karakter.',
        ]);

        $updatedData = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $updatedData['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($updatedData);

        return redirect()->route('admin.category.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->drugs()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki obat.');
        }

        $category->delete();

        return redirect()->route('admin.category.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
