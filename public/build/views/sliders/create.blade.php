@extends('layouts.app', [
    'activePage' => 'slider',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('slider.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Semua Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $storeLink }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="input_name" class="required">Nama</label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="input_name" placeholder="Masukkan nama" value="{{ old('name') }}" required>
                        @include('alerts.feedback', ['field' => 'name'])
                    </div>
                    <div class="form-group">
                        <label for="input_url" class="required">URL</label>
                        <input type="text" name="url" class="form-control {{ $errors->has('url') ? ' is-invalid' : '' }}" id="input_url" placeholder="Masukkan URL" value="{{ old('url') }}" required>
                        @include('alerts.feedback', ['field' => 'url'])
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="input_start_date" class="required">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control {{ $errors->has('start_date') ? ' is-invalid' : '' }}" id="input_start_date" placeholder="Masukkan tgl mulai" value="{{ old('start_date') }}" required>
                                @include('alerts.feedback', ['field' => 'start_date'])
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="input_end_date" class="required">Tanggal Selesai</label>
                                <input type="date" name="end_date" class="form-control {{ $errors->has('end_date') ? ' is-invalid' : '' }}" id="input_end_date" placeholder="Masukkan tgl selesai" value="{{ old('end_date') }}" required>
                                @include('alerts.feedback', ['field' => 'end_date'])
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_picture" class="required">Slider</label>
                        <input type="file" name="picture" class="form-control {{ $errors->has('picture') ? ' is-invalid' : '' }}" id="input_picture" value="{{ old('picture') }}" accept="image/*" required>
                        @include('alerts.feedback', ['field' => 'picture'])
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
