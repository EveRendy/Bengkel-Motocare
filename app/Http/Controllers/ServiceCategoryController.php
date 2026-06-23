<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Gate;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-services');
        $categories = ServiceCategory::all();
        return view('service-categories.index', compact('categories'));
    }

    public function store(ServiceCategoryRequest $request)
    {
        ServiceCategory::create($request->validated());
        return back()->with('success', 'Kategori ditambahkan');
    }
}
