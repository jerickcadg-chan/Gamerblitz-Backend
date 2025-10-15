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
            <div class="col-md-4 mb-2">
              <select id="provider_country_input" class="form-control" name="country">
                <option value="">Select Country</option>
                @foreach ($countries as $countryCode => $countryName)
                  <option value="{{ $countryCode }}"
                    {{ strtoupper(request('country')) === strtoupper($countryCode) ? 'selected' : '' }}>
                    {{ $countryName }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <div class="pt-2">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                <a href="{{ route('lapakgaming.products') }}" class="btn btn-danger btn-sm">Reset</a>
              </div>
            </div>
          </div>
        </form>
        @if ($error)
          <div class="alert alert-danger" role="alert">
            {{ $error }}
          </div>
        @endif
        @if (!is_null($products))
          <table class="table-bordered table-hover table">
            <thead>
              <tr>
                <th>No</th>
                <th>Code</th>
                <th>Name</th>
                <th>Variant</th>
                <th>Check ID</th>
                <th>Country</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse ($products as $index => $product)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $product['code'] }}</td>
                  <td>{{ $product['name'] }}</td>
                  <td>{{ $product['variant'] }}</td>
                  <td>{{ $product['check_id'] }}</td>
                  <td>{{ $product['country_code'] }}</td>
                  <td>
                    <a class="btn btn-gradient-warning btn-sm" data-bs-toggle="tooltip" title="Sync Product" data-bs-placement="top" href="{{ route('product.sync.lapak-gaming') }}"> 
                      <i class="mdi mdi-tooltip-edit menu-icon"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="100%" class="text-center">No Data</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        @else
          <h3 class="page-title mt-3"> Apply country code filter to show the data </h3>
        @endif
      </div>
    </div>
  </div>
@endsection
