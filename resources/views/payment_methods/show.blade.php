@extends('layouts.app', [
    'activePage' => 'payment_method',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('payment_method.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-nospace table-hover">
          <tr>
            <th>Name</th>
            <td>{{ $paymentMethod->name }}</td>
          </tr>
          <tr>
            <th>Slug</th>
            <td>{{ $paymentMethod->slug }}</td>
          </tr>
          <tr>
            <th>Account Name</th>
            <td>{{ $paymentMethod->account_name ?? '-' }}</td>
          </tr>
          <tr>
            <th>Account Number</th>
            <td>{{ $paymentMethod->account_number ?? '-' }}</td>
          </tr>
          <tr>
            <th>Account Holder Name</th>
            <td>{{ $paymentMethod->account_holder_name ?? '-' }}</td>
          </tr>
          <tr>
            <th>Admin Fee</th>
            <td>{{ number_format($paymentMethod->admin_fee, 2) }}</td>
          </tr>
          <tr>
            <th>Admin Type</th>
            <td>{{ ucfirst($paymentMethod->admin_type) }}</td>
          </tr>
          <tr>
            <th>Vendor</th>
            <td>{{ $paymentMethod->vendor }}</td>
          </tr>
          <tr>
            <th>Category</th>
            <td>{{ $paymentMethod->category }}</td>
          </tr>
          <tr>
            <th>Status</th>
            <td>
              @if($paymentMethod->is_active)
                <span class="badge bg-success">Active</span>
              @else
                <span class="badge bg-secondary">Inactive</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Ordering</th>
            <td>{{ $paymentMethod->ordering ?? '-' }}</td>
          </tr>
          <tr>
            <th>Created At</th>
            <td>{{ parse_date($paymentMethod->created_at) }}</td>
          </tr>
          <tr>
            <th>Updated At</th>
            <td>{{ parse_date($paymentMethod->updated_at) }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
@endsection

