@extends('layouts.app', [
    'activePage' => 'role',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('role.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $updateLink }}">
          @csrf @method('PUT')
          <div class="form-group">
            <label for="input_name">Name</label>
            <input type="text" name="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="input_name" placeholder="Enter name" value="{{ old('name', $role->name) }}" required>
            @include('alerts.feedback', ['field' => 'name'])
          </div>

          <div class="form-group">
            @include('alerts.feedback', ['field' => 'permission_id'])
            <table class="table table-hover" id="table_permission">
              <tr class="table-primary">
                <th width="50px">Checklist</th>
                <th>Permission Name</th>
              </tr>
              @foreach($permissions as $permission)
                <tr>
                  <td><input type="checkbox"
                             name="permission_id[]"
                             value="{{ $permission->id }}"
                             @if($role->permissions->contains($permission)) checked @endif>
                  </td>
                  <td>{{ $permission->name }}</td>
                </tr>
              @endforeach
            </table>
          </div>

          <button type="submit" class="btn btn-primary">Submit</button>
          <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection
