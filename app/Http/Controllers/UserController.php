<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'User';

        $this->middleware(['permission:View User'])->only('index', 'show');
        $this->middleware(['permission:Create User'])->only(['create', 'store']);
        $this->middleware(['permission:Edit User'])->only('edit', 'update');
        $this->middleware(['permission:Delete User'])->only('destroy');
    }

    public function index()
    {
        $users = User::nonCustomer()->latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%'. request('name') .'%');
            })
            ->paginate();

        $createLink = route('user.create');

        $title = $this->title;

        return view('users.index', compact('users', 'createLink', 'title'));
    }

    public function getCustomer()
    {
        $users = User::customer()->latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%'. request('name') .'%');
            })
            ->paginate();

        $title = $this->title;

        return view('users.customer', compact('users', 'title'));
    }

    public function create()
    {
        $storeLink = route('user.store');
        $indexLink = route('user.index');

        $title = $this->title;

        $roles = Role::all();

        return view('users.create', compact('roles', 'storeLink', 'indexLink', 'title'));
    }

    public function show(User $user)
    {
        $editLink = route('user.edit', $user);
        $deleteLink = route('user.destroy', $user);
        $indexLink = route('user.index');

        $title = $this->title;

        return view('users.show', compact('user', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->all());

        $user->assignRole($request->role_id);

        toast(alert_created_text($this->title),'success');
        return redirect()->route('user.index');
    }

    public function edit(User $user)
    {
        $updateLink = route('user.update', $user);
        $indexLink = route('user.index');

        $title = $this->title;

        $roles = Role::all();

        return view('users.edit', compact('roles', 'updateLink', 'indexLink', 'user', 'title'));
    }

    public function update(UserRequest $request, User $user)
    {
        $user->update($request->except([$request->password ? '' : 'password']));

        $user->syncRoles($request->role_id);

        toast(alert_updated_text($this->title),'success');
        return redirect()->route('user.index');
    }

    public function destroy(User $user)
    {
        $user->delete();

        toast(alert_deleted_text($this->title),'success');
        return redirect()->route('user.index');
    }
}
