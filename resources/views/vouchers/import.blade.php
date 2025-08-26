@extends('layouts.app', [
    'activePage' => 'voucher',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Page {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('voucher.index', ['product_item_id' => request('product_item_id')]) }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $importLink }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="input_excel">Upload file excel</label>
                        <input type="file" name="excel" class="form-control {{ $errors->has('excel') ? ' is-invalid' : '' }}" id="input_excel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                        @include('alerts.feedback', ['field' => 'excel'])
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
                </form>
                <div class="form-group mt-3">
                    <p><b>Format Excel</b></p>
                    <table class="table table-bordered">
                        <tr>
                            <th>product_item_id</th>
                            <th>serial_number</th>
                            <th>password</th>
                            <th>capital</th>
                            <th>vendor</th>
                        </tr>
                        <tr>
                            <td>12</td>
                            <td>1000111</td>
                            <td>3029019318923123</td>
                            <td>17500</td>
                            <td>Unipin</td>
                        </tr>
                        <tr>
                            <td>12</td>
                            <td>109023123</td>
                            <td>9301293849012312</td>
                            <td>17500</td>
                            <td>Unipin</td>
                        </tr>
                        <tr>
                            <td>12</td>
                            <td>109023458</td>
                            <td>7897899912331235</td>
                            <td>17500</td>
                            <td>Unipin</td>
                        </tr>
                    </table>
                    <p class="text-danger mt-2">Note: Tidak perlu mengisi judul kolom pada excel, cukup masukkan dengan data voucher</p>
                </div>
            </div>
        </div>
    </div>
@endsection
