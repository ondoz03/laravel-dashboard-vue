<?php

namespace App\Http\Controllers\User;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index(Request $request): Response
    {
        $query = Permission::query();

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Apply sorting
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');

        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['id', 'name', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'name';
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

        $permissions = $query->paginate($perPage)->withQueryString();

        return Inertia::render('User/Permission/Index', [
            'permissions' => [
                'data' => $permissions->items(),
                'meta' => PaginationHelper::getMetaData($permissions, (int)$perPage),
            ],
            'filters' => [
                'search' => $request->input('search', ''),
            ],
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
            'allowedPerPageValues' => $allowedPerPageValues,
            'route-path' => route('permissions.index', [], false),
        ]);
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create(): Response
    {
        return Inertia::render('User/Permission/Create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::create(['name' => $validated['name']]);

        return Redirect::route('permissions.index')->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission): Response
    {
        $permission->load('roles');

        return Inertia::render('User/Permission/Show', [
            'permission' => $permission,
        ]);
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission): Response
    {
        return Inertia::render('User/Permission/Edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
        ]);

        $permission->update(['name' => $validated['name']]);

        return Redirect::route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return Redirect::route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
