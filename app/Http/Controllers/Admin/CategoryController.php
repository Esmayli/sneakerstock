<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    private const IMAGE_VALIDATION_RULE = 'nullable|mimetypes:image/jpeg,image/png,image/gif,image/bmp,image/webp,image/avif';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('id', 'desc')->get();

        return view('admin.categories.index', compact('categories'));
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'image' => self::IMAGE_VALIDATION_RULE,
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categorias', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
        return view('admin.categories.edit', compact('category'));
    }

    public function image(Category $category)
    {
        abort_unless($category->image_path && Storage::disk('public')->exists($category->image_path), 404);

        return Storage::disk('public')->response($category->image_path);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'image' => self::IMAGE_VALIDATION_RULE,
        ]);
        $oldImagePath = $category->image_path;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categorias', 'public');
        }

        $category->update($data);

        if ($request->hasFile('image') && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        session()->flash(
            'swal', [
                'title' => 'Bien hecho',
                'text' => 'La categoria se a actualizado correctamente',
                'icon' => 'success',
                'confirmButtonText' => 'Aceptar',
            ]
        );

        return redirect()->route('admin.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();
        session()->flash(
            'swal', [
                'title' => 'Bien hecho',
                'text' => 'La categoria se a eliminado correctamente',
                'icon' => 'success',
                'confirmButtonText' => 'Aceptar',
            ]
        );

        return redirect()->route('admin.categories.index');
    }
}
