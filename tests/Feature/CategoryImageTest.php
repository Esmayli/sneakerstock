<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

test('admin can upload and display a category image', function () {
    Storage::fake('public');
    $admin = User::factory()->create();
    $admin->assignRole(Role::create(['name' => 'admin']));

    $this->actingAs($admin)
        ->post(route('admin.categories.store'), [
            'name' => 'Running',
            'image' => UploadedFile::fake()->createWithContent('running.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a5WQAAAAASUVORK5CYII=')),
        ])
        ->assertRedirect(route('admin.categories.index'));

    $category = Category::query()->where('name', 'Running')->firstOrFail();

    Storage::disk('public')->assertExists($category->image_path);
    $this->actingAs($admin)->get(route('admin.categories.image', $category))->assertOk();
});

test('admin can replace a category image without leaving the old file behind', function () {
    Storage::fake('public');
    $admin = User::factory()->create();
    $admin->assignRole(Role::create(['name' => 'admin']));
    $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a5WQAAAAASUVORK5CYII=');
    $oldImagePath = UploadedFile::fake()->createWithContent('old.png', $imageContent)->store('categorias', 'public');
    $category = Category::query()->create([
        'name' => 'Casual',
        'image_path' => $oldImagePath,
    ]);

    $this->actingAs($admin)
        ->put(route('admin.categories.update', $category), [
            'name' => 'Casual',
            'image' => UploadedFile::fake()->createWithContent('new.png', $imageContent),
        ])
        ->assertRedirect(route('admin.categories.index'));

    $category->refresh();

    expect($category->image_path)->not->toBe($oldImagePath);
    Storage::disk('public')->assertExists($category->image_path);
    Storage::disk('public')->assertMissing($oldImagePath);
});