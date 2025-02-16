@extends('layouts.app', [
    'activePage' => 'payment_method',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
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
                <table class="table table-nospace">
                    <tr>
                        <th>Nama</th>
                        <td>{{ $payment_method->name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection

