@extends('layouts.app', [
    'activePage' => 'discount',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('discount.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <table class="table table-nospace">
                    <tr>
                        <th width="200">Nama</th>
                        <td>{{ $discount->name }}</td>
                    </tr>
                    <tr>
                        <th>Kode</th>
                        <td>{{ $discount->code }}</td>
                    </tr>
                    <tr>
                        <th>Nominal</th>
                        <td>{{ $discount->discount }}</td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>{{ parse_date($discount->start_date) }} - {{ parse_date($discount->end_date) }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{!! $discount->description !!}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{!! $discount->status_label !!}</td>
                    </tr>
                    <tr>
                        <th>Maksimal Penggunaan</th>
                        <td>{{ currency_format($discount->maximum) }}</td>
                    </tr>
                    <tr>
                        <th>Digunakan</th>
                        <td>{{ currency_format($discount->used) }}</td>
                    </tr>
                    <tr>
                        <th>Tgl Dibuat</th>
                        <td>{{ parse_date_time($discount->created_at) }}</td>
                    </tr>
                    <tr>
                        <th>Tgl Diupdate</th>
                        <td>{{ parse_date_time($discount->updated_at) }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Produk</th>
                        <td>{{ $discount->product_type_desc }}</td>
                    </tr>
                    @if ($discount->product_type != \App\Models\Discount::ALL)
                        <tr>
                            <th>List Produk</th>
                            <td>
                                <ul>
                                    @foreach ($discount->products as $product)
                                    <li>{{ $product->productable->product ? $product->productable->product->name.' - ' : null }} {{ $product->productable->name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
