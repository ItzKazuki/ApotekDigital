<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Drug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DrugController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all drugs from the database
        $categories = Category::all();

        $category = request()->query('category'); // from ?category=Name
        $search = request()->query('search'); // from ?search=DrugName

        $drugs = Drug::when($category, function ($query) use ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('id', $category);
            });
        })
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->get();

        // Return the view with the drugs data
        return view('admin.drugs.index', compact('drugs', 'categories'));
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
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255|unique:drugs,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'purchase_price' => 'required|numeric',
            'modal' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp',
            'expired_at' => 'required|date',
            'barcode' => 'nullable|string|unique:drugs,barcode',
            'packaging_types' => 'required|in:strip,botol,ampul,sachet,vial,tube,suppositoria,inhaler,patch,box'
        ]);

        // Create a new drug instance and save it to the database
        Drug::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'purchase_price' => $request->input('purchase_price', 0), // Default to 0 if not provided
            'modal' => $request->input('modal', 0), // Default to 0 if not provided
            'stock' => $request->input('quantity', 0), // Default to 0 if not provided
            'category_id' => $request->input('category_id'), // Ensure category_id is provided
            'image_path' => $request->file('image') ? $request->file('image')->store(Drug::DRUG_IMAGE_PATH, 'public') : null,
            'expired_at' => $request->input('expired_at') ? now()->parse($request->input('expired_at')) : null,
            'barcode' => $request->input('barcode', null), // Allow barcode to be
            'packaging_types' => $request->input('packaging_types')
        ]);

        // Redirect back to the drugs index with a success message
        return redirect()->route('admin.drug.index')->with('success', 'Obat berhasil ditambah.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Drug $drug)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Drug $drug)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Drug $drug)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255|unique:drugs,name,' . $drug->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'purchase_price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp',
            'expired_at' => 'required|date',
            'packaging_types' => 'required|in:strip,botol,ampul,sachet,vial,tube,suppositoria,inhaler,patch,box'
        ]);

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'purchase_price' => $request->input('purchase_price', 0),
            'stock' => $request->input('quantity', 0),
            'category_id' => $request->input('category_id'),
            'expired_at' => $request->input('expired_at') ? now()->parse($request->input('expired_at')) : null,
            'packaging_types' => $request->input('packaging_types')
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($drug->image_path) {
                Storage::disk('public')->delete($drug->image_path);
            }
            $data['image_path'] = $request->file('image')->store(Drug::DRUG_IMAGE_PATH, 'public');
        }

        // Update the drug instance with the validated data
        $drug->update($data);

        // Redirect back to the drugs index with a success message
        return redirect()->route('admin.drug.index')->with('success', 'Obat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Drug $drug)
    {
        if (!$drug) {
            return redirect()->route('admin.drug.index')->with('error', 'Obat tidak ditemukan.');
        }

        if ($drug->stock >= 1) {
            return redirect()->route('admin.drug.index')->with('error', 'tidak dapat menghapus obat yang masih memiliki stok.');
        }

        if ($drug->image_path) {
            // Delete the image file if it exists
            Storage::disk('public')->delete($drug->image_path);
        }

        // Delete the drug instance from the database
        $drug->delete();

        // Redirect back to the drugs index with a success message
        return redirect()->route('admin.drug.index')->with('success', 'Obat berhasil dihapus.');
    }
}
