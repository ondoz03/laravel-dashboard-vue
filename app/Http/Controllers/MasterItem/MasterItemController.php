<?php

namespace App\Http\Controllers\MasterItem;

use App\Http\Controllers\Controller;
use App\Models\MasterItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MasterItemController extends Controller
{
    /**
     * Display a listing of the master items.
     */
    public function index(Request $request): Response
    {
        $query = MasterItem::query();

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('item_category', 'like', "%{$search}%")
                  ->orWhere('buyer', 'like', "%{$search}%");
            });
        }

        if ($request->has('category')) {
            $query->where('item_category', $request->input('category'));
        }

        if ($request->has('buyer')) {
            $query->where('buyer', $request->input('buyer'));
        }

        // Apply sorting
        $sortField = $request->input('sort', 'item_code');
        $sortDirection = $request->input('direction', 'asc');

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['item_code', 'item_name', 'item_category', 'buyer', 'ppn', 'pph'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'item_code';
        }

        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortField, $sortDirection);

        // Get per page value from request or use default
        $perPage = $request->input('per_page', 10);

        // Validate per page value
        $allowedPerPageValues = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPageValues)) {
            $perPage = 10;
        }

        $masterItems = $query->paginate($perPage)->withQueryString();

        // Get unique categories and buyers for filters
        $categories = MasterItem::distinct()->pluck('item_category')->filter()->values();
        $buyers = MasterItem::distinct()->pluck('buyer')->filter()->values();

        return Inertia::render('MasterItem/Index', [
            'masterItems' => $masterItems,
            'filters' => [
                'search' => $request->input('search', ''),
                'category' => $request->input('category', ''),
                'buyer' => $request->input('buyer', ''),
            ],
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
            'categories' => $categories,
            'buyers' => $buyers,
        ]);
    }

    /**
     * Show the form for creating a new master item.
     */
    public function create(): Response
    {
        return Inertia::render('MasterItem/Create');
    }

    /**
     * Store a newly created master item in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_code' => ['required', 'string', 'max:255', 'unique:master_items'],
            'item_name' => ['required', 'string', 'max:255'],
            'category_item_id' => ['nullable', 'numeric'],
            'aol_id' => ['nullable', 'string', 'max:255'],
            'item_category' => ['nullable', 'string', 'max:255'],
            'buyer' => ['nullable', 'string', 'max:255'],
            'ppn' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pph' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        MasterItem::create($validated);

        return Redirect::route('master-items.index')->with('success', 'Master item created successfully.');
    }

    /**
     * Display the specified master item.
     */
    public function show(MasterItem $masterItem): Response
    {
        return Inertia::render('MasterItem/Show', [
            'masterItem' => $masterItem,
        ]);
    }

    /**
     * Show the form for editing the specified master item.
     */
    public function edit(MasterItem $masterItem): Response
    {
        return Inertia::render('MasterItem/Edit', [
            'masterItem' => $masterItem,
        ]);
    }

    /**
     * Update the specified master item in storage.
     */
    public function update(Request $request, MasterItem $masterItem): RedirectResponse
    {
        $validated = $request->validate([
            'item_code' => ['required', 'string', 'max:255', Rule::unique('master_items')->ignore($masterItem->id)],
            'item_name' => ['required', 'string', 'max:255'],
            'category_item_id' => ['nullable', 'numeric'],
            'aol_id' => ['nullable', 'string', 'max:255'],
            'item_category' => ['nullable', 'string', 'max:255'],
            'buyer' => ['nullable', 'string', 'max:255'],
            'ppn' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pph' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $masterItem->update($validated);

        return Redirect::route('master-items.index')->with('success', 'Master item updated successfully.');
    }

    /**
     * Remove the specified master item from storage.
     */
    public function destroy(MasterItem $masterItem): RedirectResponse
    {
        $masterItem->delete();

        return Redirect::route('master-items.index')->with('success', 'Master item deleted successfully.');
    }
}
