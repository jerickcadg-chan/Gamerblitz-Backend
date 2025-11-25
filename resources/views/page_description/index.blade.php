@extends('layouts.app', ['activePage' => 'page_description'])

@section('content')
  <div class="page-header d-block mb-3">
    <a href="{{ route('page-descriptions.create') }}" class="btn btn-primary">New Page Description</a>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('page-descriptions.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">All Data</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table-bordered table-hover table-responsive table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Slug</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pageDescriptions as $page)
                <tr>
                  <td>{{ $page->name }}</td>
                  <td>{{ $page->slug }}</td>
                  <td class="text-end">
                    @include('master.action', [
                        'edit_url' => route('page-descriptions.edit', $page),
                        'delete_url' => route('page-descriptions.destroy', $page),
                    ])
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="100%" class="text-muted text-center">No data.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-3">{{ $pageDescriptions->links() }}</div>
      </div>
    </div>
  </div>
@endsection
