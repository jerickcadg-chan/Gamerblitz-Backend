@extends('layouts.app', [
    'activePage' => 'role',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('role.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <table class="table table-nospace">
          <tr>
            <th>Name</th>
            <td>{{ $role->name }}</td>
          </tr>
          <tr>
            <th>Created At</th>
            <td>{{ parse_date_time($role->created_at) }}</td>
          </tr>
          <tr>
            <th>Updated At</th>
            <td>{{ parse_date_time($role->updated_at) }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-header">
        <h4>Permissions</h4>
      </div>
      <div class="card-body">
        <table class="table table-nospace">
          @foreach ($role->permissions as $permission)
            <tr>
              <td width="30px">{{ $loop->iteration }}</td>
              <td>{{ $permission->name }}</td>
            </tr>
          @endforeach
        </table>
      </div>
    </div>
  </div>
@endsection
