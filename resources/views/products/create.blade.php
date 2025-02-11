{{-- @extends('layouts.app', [ --}}
{{--     'activePage' => 'product', --}}
{{-- ]) --}}
{{----}}
{{-- @section('content') --}}
{{--     <div class="page-header"> --}}
{{--         <h3 class="page-title"> Halaman {{ $title }} </h3> --}}
{{--         <nav aria-label="breadcrumb"> --}}
{{--             <ol class="breadcrumb"> --}}
{{--                 <li class="breadcrumb-item"><a href="{{ route('product.index') }}">{{ $title }}</a></li> --}}
{{--                 <li class="breadcrumb-item active" aria-current="page">Tambah Data</li> --}}
{{--             </ol> --}}
{{--         </nav> --}}
{{--     </div> --}}
{{----}}
{{--     <div class="col-lg-12 grid-margin stretch-card"> --}}
{{--         <div class="card"> --}}
{{--             <div class="card-body"> --}}
{{--                 <form method="POST" action="{{ $storeLink }}" enctype="multipart/form-data"> --}}
{{--                     @csrf --}}
{{--                     <div class="form-group"> --}}
{{--                         <label for="name_input" class="required">Nama</label> --}}
{{--                         <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="name_input" placeholder="Masukkan Nama" value="{{ old('name') }}" required> --}}
{{--                         @include('alerts.feedback', ['field' => 'name']) --}}
{{--                     </div> --}}
{{--                     <div class="form-group"> --}}
{{--                         <label for="code_input">Kode</label> --}}
{{--                         <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" id="code_input" placeholder="Masukkan Kode" value="{{ old('code') }}"> --}}
{{--                         @include('alerts.feedback', ['field' => 'code']) --}}
{{--                     </div> --}}
{{--                     <div class="form-group"> --}}
{{--                         <label for="company_input" class="required">Perusahaan</label> --}}
{{--                         <input type="text" class="form-control {{ $errors->has('company') ? ' is-invalid' : '' }}" name="company" id="company_input" value="{{ old('company') }}" placeholder="Montoon" required> --}}
{{--                         @include('alerts.feedback', ['field' => 'company']) --}}
{{--                     </div> --}}
{{--                     <div class="row"> --}}
{{--                         <div class="col-md-6"> --}}
{{--                             <div class="form-group"> --}}
{{--                                 <label for="markup_user_input" class="required">Markup User (%)</label> --}}
{{--                                 <input type="number" min="0" class="form-control {{ $errors->has('markup_user') ? ' is-invalid' : '' }}" name="markup_user" id="markup_user_input" value="{{ old('markup_user') }}" placeholder="Montoon" required> --}}
{{--                                 @include('alerts.feedback', ['field' => 'markup_user']) --}}
{{--                             </div> --}}
{{--                         </div> --}}
{{--                         <div class="col-md-6"> --}}
{{--                             <div class="form-group"> --}}
{{--                                 <label for="markup_reseller_input" class="required">Markup Reseller (%)</label> --}}
{{--                                 <input type="number" min="0" class="form-control {{ $errors->has('markup_reseller') ? ' is-invalid' : '' }}" name="markup_reseller" id="markup_reseller_input" value="{{ old('markup_reseller') }}" placeholder="Montoon" required> --}}
{{--                                 @include('alerts.feedback', ['field' => 'markup_reseller']) --}}
{{--                             </div> --}}
{{--                         </div> --}}
{{--                     </div> --}}
{{--                     <div class="form-group"> --}}
{{--                         <label for="category_input" class="required">Kategori</label> --}}
{{--                         <select class="form-control {{ $errors->has('category') ? ' is-invalid' : '' }}" name="category" id="category_input" required> --}}
{{--                             <option value="">Pilih kategori</option> --}}
{{--                             @foreach ($productCategories as $key => $category) --}}
{{--                                 <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : null }}>{{ ucfirst($category) }}</option> --}}
{{--                             @endforeach --}}
{{--                         </select> --}}
{{--                         @include('alerts.feedback', ['field' => 'category']) --}}
{{--                     </div> --}}
{{--                     <div class="form-group"> --}}
{{--                         <label for="product_joki">Produk Joki</label> --}}
{{--                         <select class="form-control {{ $errors->has('product_joki') ? ' is-invalid' : '' }}" name="product_joki" id="product_joki"> --}}
{{--                             <option value="">Jika bukan joki abaikan</option> --}}
{{--                             @foreach ($productJoki as $key => $category) --}}
{{--                                 <option value="{{ $key }}" {{ old('product_joki') == $key ? 'selected' : null }}>{{ ucfirst($category) }}</option> --}}
{{--                             @endforeach --}}
{{--                         </select> --}}
{{--                         @include('alerts.feedback', ['field' => 'product_joki']) --}}
{{--                     </div> --}}
{{--                     <div class="form-group"> --}}
{{--                         <label for="description_input" class="required">Deskripsi</label> --}}
{{--                         <textarea class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }} tinymce" name="description" id="description_input" placeholder="Masukkan Deskripsi">{{ old('description') }}</textarea> --}}
{{--                         @include('alerts.feedback', ['field' => 'description']) --}}
{{--                     </div> --}}
{{--                     <div class="form-group"> --}}
{{--                         <label for="input_format_input" class="required">Format Input</label> --}}
{{--                         <textarea class="form-control" name="input_format" id="input_format_input" placeholder="Masukkan Format Input">{{ old('input_format') }}</textarea> --}}
{{--                         @include('alerts.feedback', ['field' => 'input_format']) --}}
{{--                     </div> --}}
{{--                     <div class="form-group"> --}}
{{--                         <label for="how_to_order_input" class="required">Cara Order</label> --}}
{{--                         <textarea class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }} tinymce" name="how_to_order" id="how_to_order_input" placeholder="Masukkan Cara Order">{{ old('how_to_order') }}</textarea> --}}
{{--                         @include('alerts.feedback', ['field' => 'how_to_order']) --}}
{{--                     </div> --}}
{{--                     <div class="form-group"> --}}
{{--                         <label for="cover" class="required">Cover</label> --}}
{{--                         <input type="file" name="cover" class="form-control" accept="image/*" value="{{ old('cover') }}"> --}}
{{--                         @include('alerts.feedback', ['field' => 'cover']) --}}
{{--                     </div> --}}
{{--                     <div class="form-group"> --}}
{{--                         <label for="picture" class="required">Gambar</label> --}}
{{--                         <input type="file" name="picture" class="form-control" accept="image/*" value="{{ old('picture') }}"> --}}
{{--                         @include('alerts.feedback', ['field' => 'picture']) --}}
{{--                     </div> --}}
{{--                     <input type="hidden" name="status" value="active"> --}}
{{--                     <button type="submit" class="btn btn-gradient-primary me-2">Submit</button> --}}
{{--                     <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a> --}}
{{--                 </form> --}}
{{--             </div> --}}
{{--         </div> --}}
{{--     </div> --}}
{{-- @endsection --}}
{{----}}
{{-- @push('js') --}}
{{-- <script src="https://cdn.tiny.cloud/1/wejmk4ubc4t2ncovd3risw07yelp0dwzbvdxjq1ilyoizq6p/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> --}}
{{-- <script> --}}
{{--     tinymce.init({ --}}
{{--         selector:'textarea.tinymce', --}}
{{--         height: 300 --}}
{{--     }); --}}
{{-- </script> --}}
{{-- @endpush --}}
