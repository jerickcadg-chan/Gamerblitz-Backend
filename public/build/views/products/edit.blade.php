@extends('layouts.app', [
    'activePage' => 'product',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('product.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $updateLink }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="form-group">
                        <label for="name_input" class="required">Nama</label>
                        <input type="text" class="form-control" name="name" id="name_input" placeholder="Masukkan Nama" value="{{ old('name', $product->name) }}" required>
                        @include('alerts.feedback', ['field' => 'name'])
                    </div>
                    <div class="form-group">
                        <label for="code_input">Kode</label>
                        <input type="text" class="form-control" name="code" id="code_input" placeholder="Masukkan Nama" value="{{ old('code', $product->code) }}">
                        @include('alerts.feedback', ['field' => 'code'])
                    </div>
                    <div class="form-group">
                        <label for="company_input" class="required">Perusahaan</label>
                        <input type="text" class="form-control" name="company" id="company_input" value="{{ old('company', $product->company) }}" placeholder="Diamond, Membership" required>
                        @include('alerts.feedback', ['field' => 'company'])
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="markup_user_input" class="required">Markup User (%)</label>
                                <input type="number" min="0" class="form-control {{ $errors->has('markup_user') ? ' is-invalid' : '' }}" name="markup_user" id="markup_user_input" value="{{ old('markup_user', $product->markup_user) }}" placeholder="Markup Harga Untuk User" required>
                                @include('alerts.feedback', ['field' => 'markup_user'])
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="markup_reseller_input" class="required">Markup Reseller (%)</label>
                                <input type="number" min="0" class="form-control {{ $errors->has('markup_reseller') ? ' is-invalid' : '' }}" name="markup_reseller" id="markup_reseller_input" value="{{ old('markup_reseller', $product->markup_reseller) }}" placeholder="Markup Harga Untuk Reseller" required>
                                @include('alerts.feedback', ['field' => 'markup_reseller'])
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="category_input" class="required">Kategori</label>
                        <select class="form-control" name="category" id="category_input" required>
                            <option value="">Pilih kategori</option>
                            @foreach (config('array.product.category') as $category)
                                <option value="{{ $category }}" {{ old('category', $product->category) == $category ? 'selected' : null }}>{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>
                        @include('alerts.feedback', ['field' => 'category'])
                    </div>
                    <div class="form-group">
                        <label for="description_input" class="required">Deskripsi</label>
                        <textarea class="form-control tinymce" name="description" id="description_input" placeholder="Masukkan Deskripsi">{{ old('description', $product->description) }}</textarea>
                        @include('alerts.feedback', ['field' => 'description'])
                    </div>
                    <div class="form-group">
                        <label for="input_format_input" class="required">Format Input</label>
                        <textarea class="form-control" name="input_format" id="input_format_input" placeholder="Masukkan Format Input">{{ old('input_format', $product->input_format) }}</textarea>
                        @include('alerts.feedback', ['field' => 'input_format'])
                    </div>
                    <div class="form-group">
                        <label for="how_to_order_input" class="required">Cara Order</label>
                        <textarea class="form-control tinymce" name="how_to_order" id="how_to_order_input" placeholder="Masukkan Cara Order">{{ old('how_to_order', $product->how_to_order) }}</textarea>
                        @include('alerts.feedback', ['field' => 'how_to_order'])
                    </div>
                    <div class="form-group">
                        <label for="picture">Cover</label>
                        <input type="file" name="picture" class="form-control mb-2" accept="image/*" value="{{ old('picture') }}">
                        <small><i>Kosongi apabila tidak merubah cover</i></small>
                        @include('alerts.feedback', ['field' => 'picture'])
                    </div>
                    <div class="form-group">
                        <label for="status_input" class="required">Status</label>
                        <select class="form-control" name="status" id="status_input" required>
                            <option value="">Pilih Status</option>
                            @foreach (config('array.product.status') as $status)
                                <option value="{{ $status }}" {{ old('status', $product->status) == $status ? 'selected' : null }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        @include('alerts.feedback', ['field' => 'status'])
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                    <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.tiny.cloud/1/wejmk4ubc4t2ncovd3risw07yelp0dwzbvdxjq1ilyoizq6p/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector:'textarea.tinymce',
        height: 300
    });
</script>
@endpush
