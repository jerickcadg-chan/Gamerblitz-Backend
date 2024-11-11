<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->title = 'Hak Akses';

        $this->middleware(['permission:View Role'])->only('index', 'show');
        $this->middleware(['permission:Create Role'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Role'])->only('edit', 'update');
        $this->middleware(['permission:Delete Role'])->only('destroy');
    }

    public function index()
    {
        $roles = Role::latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%'. request('name') .'%');
            })
            ->paginate();

        $createLink = route('role.create');

        $title = $this->title;

        return view('roles.index', compact('roles', 'createLink', 'title'));
    }

    public function create()
    {
        $storeLink = route('role.store');
        $indexLink = route('role.index');

        $title = $this->title;

        $permissions = Permission::all();

        return view('roles.create', compact('permissions', 'storeLink', 'indexLink', 'title'));
    }

    public function show(Role $role)
    {
        $editLink = route('role.edit', $role);
        $deleteLink = route('role.destroy', $role);
        $indexLink = route('role.index');

        $title = $this->title;

        return view('roles.show', compact('role', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(RoleRequest $request)
    {
        DB::beginTransaction();

        $role = Role::create([
            'name' => $request->name
        ]);

        $permissions = Permission::whereIn('id', $request->permission_id)->get();

        $role->givePermissionTo($permissions);

        DB::commit();

        toast(alert_created_text($this->title),'success');
        return redirect()->route('role.index');
    }

    public function edit(Role $role)
    {
        $updateLink = route('role.update', $role);
        $indexLink = route('role.index');

        $title = $this->title;

        $permissions = Permission::all();

        return view('roles.edit', compact('permissions', 'updateLink', 'indexLink', 'role', 'title'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        if (collect(config('array.default_role'))->contains($role->name)) {
            abort(403);
        }

        $role->update($request->all());

        $permissions = Permission::whereIn('id', $request->permission_id)->get();

        $role->syncPermissions($permissions);

        toast(alert_updated_text($this->title),'success');
        return redirect()->route('role.index');
    }

    public function destroy(Role $role)
    {
        if (collect(config('array.default_role'))->contains($role->name)) {
            abort(403);
        }

        $role->delete();

        toast(alert_deleted_text($this->title),'success');
        return redirect()->route('role.index');
    }
}
