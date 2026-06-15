<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-services');
        $categories = ServiceCategory::all();
        return view('service-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-services');
        $request->validate(['name' => 'required|string']);
        ServiceCategory::create($request->all());
        return back()->with('success', 'Kategori ditambahkan');
    }
}