@extends('layouts.app', ['activePage' => 'flash_sale'])

@section('content')
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">

        <form method="GET" action="{{ route('flash_sale.create') }}" class="mb-3">
          <div class="row g-2 align-items-end">
            <div class="col-md-6">
              <label class="form-label">Filter Product</label>
              <select name="product_id" id="product_filter" class="form-control select2" style="width:100%">
                <option value="">— Select product —</option>
                @foreach($products as $product)
                  <option value="{{ $product->id }}" {{ (string)request('product_id')===(string)$product->id ? 'selected' : '' }}>
                    {{ $product->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
            @if(request()->filled('product_id'))
              <div class="col-md-2">
                <a href="{{ route('flash_sale.create') }}" class="btn btn-light w-100">Reset</a>
              </div>
            @endif
          </div>
        </form>

        {{-- FORM (hanya tampil jika ada filter product_id) --}}
        @if(request()->filled('product_id'))
          <form method="POST" action="{{ $storeLink }}">
            @csrf

            <div class="mb-2">
              <input type="text" id="product_item_search" class="form-control" placeholder="Search item…" oninput="filterRows()">
            </div>

            <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
              <table class="table table-sm table-striped align-middle">
                <thead>
                <tr>
                  <th style="width:36px;"></th>
                  <th>Item</th>
                  <th class="text-end">Harga Normal</th>
                  <th style="width:160px;">Flash Price</th>
                  <th style="width:140px;">Stock</th>
                </tr>
                </thead>
                <tbody id="itemsBody">
                @forelse($productItems as $productItem)
                  <tr class="item-row" data-text="{{ Str::lower($productItem->name) }}">
                    <td>
                      <input type="checkbox" class="form-check-input fs-check"
                             name="items[{{ $productItem->id }}][selected]" value="1"
                             {{ old("items.$productItem->id.selected") ? 'checked' : '' }}
                             onchange="toggleRow(this)">
                    </td>
                    <td>{{ $productItem->name }}</td>
                    <td class="text-end">{{ currency_format($productItem->price ?? 0) }}</td>
                    <td>
                      <input type="number" min="0" step="1"
                             class="form-control form-control-sm fs-price {{ $errors->has("items.$productItem->id.price") ? 'is-invalid' : '' }}"
                             name="items[{{ $productItem->id }}][price]"
                             value="{{ old("items.$productItem->id.price") }}"
                        {{ old("items.$productItem->id.selected") ? '' : 'disabled' }}>
                    </td>
                    <td>
                      <input type="number" min="0" step="1"
                             class="form-control form-control-sm fs-stock {{ $errors->has("items.$productItem->id.stock") ? 'is-invalid' : '' }}"
                             name="items[{{ $productItem->id }}][stock]"
                             value="{{ old("items.$productItem->id.stock") }}"
                        {{ old("items.$productItem->id.selected") ? '' : 'disabled' }}>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center text-muted">Tidak ada item untuk produk ini.</td></tr>
                @endforelse
                </tbody>
              </table>
            </div>

            @include('alerts.feedback', ['field' => 'items'])

            <button type="submit" class="btn btn-primary mt-3">Submit</button>
            <a href="{{ $indexLink }}" class="btn btn-light mt-3">Cancel</a>
          </form>
        @endif

      </div>
    </div>
  </div>
@endsection

@push('js')
  <script>
    function toggleRow(checkbox){
      const tr = checkbox.closest('tr');
      const inputs = tr.querySelectorAll('.fs-price, .fs-stock');
      inputs.forEach(inp => {
        inp.disabled = !checkbox.checked;
        if(!checkbox.checked){ inp.value = ''; }
      });
    }

    function filterRows(){
      const q = (document.getElementById('product_item_search').value || '').toLowerCase();
      document.querySelectorAll('#itemsBody .item-row').forEach(row => {
        row.style.display = !q || row.dataset.text.includes(q) ? '' : 'none';
      });
    }
  </script>
@endpush
