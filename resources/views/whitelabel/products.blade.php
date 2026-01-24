@extends('layouts.app', [
    'activePage' => 'lapakgaming.products',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('lapakgaming.products') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <form method="get">
          <div class="row mb-2">
            @if (request('country'))
              <div class="col-md-4 mb-2">
                <input type="text" class="form-control" id="searchInput" name="name"
                  placeholder="Search product name" value="{{ request('name') }}">
              </div>
            @endif
            <div class="col-md-4">
              <div class="pt-2">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                <a href="{{ route('whitelabel.products') }}" class="btn btn-danger btn-sm">Reset</a>
              </div>
            </div>
          </div>
        </form>
        @if ($error)
          <div class="alert alert-danger" role="alert">
            {{ $error }}
          </div>
        @endif
        <table class="table-bordered table-hover table" id="productsTable">
          <thead>
            <tr>
              <th>No</th>
              <th>Code</th>
              <th>Name</th>
              <th>Category</th>
              <th>Company</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($products as $index => $product)
              @php
                $code = strtoupper($product['id']);
                $alreadyExists = $existingCodes->contains($code);
              @endphp
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product['id'] }}</td>
                <td>{{ $product['name'] }}</td>
                <td>{{ $product['category'] }}</td>
                <td>{{ $product['company'] }}</td>
                <td>
                  @if ($alreadyExists)
                    <button class="btn btn-success btn-sm" disabled>
                      <i class="mdi mdi-check-circle menu-icon"></i>
                      Added
                    </button>
                  @else
                    <a class="btn btn-gradient-warning btn-sm" data-bs-toggle="tooltip" title="Sync to Product Forms"
                      data-bs-placement="top" target="_blank"
                      href="{{ route('product.sync.whitelabel', ['whitelabel_code' => $product['id']]) }}">
                      <i class="mdi mdi-tooltip-edit menu-icon"></i>
                    </a>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="100%" class="text-center">No Data</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      const table = document.getElementById('productsTable');

      if (!searchInput || !table) console.log(table); // amanin biar gak error

      const rows = table.querySelectorAll('tbody tr');

      searchInput.addEventListener('keyup', function() {
        const keyword = this.value.toLowerCase();

        rows.forEach(row => {
          const nameCell = row.querySelector('td:nth-child(3)');
          const name = nameCell ? nameCell.textContent.toLowerCase() : '';
          row.style.display = name.includes(keyword) ? '' : 'none';
        });
      });
    });
  </script>
@endpush
