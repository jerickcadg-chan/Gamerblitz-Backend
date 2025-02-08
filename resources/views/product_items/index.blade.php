@extends('layouts.app', [
    'activePage' => 'product_item',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('product_item.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Semua Data</li>
            </ol>
        </nav>
    </div>

    @include('product_items.filter')

    <div class="modal fade" id="updateMarginActionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method="POST" id="form-update-margin">
            <div class="modal-header">
              <h5 class="modal-title">Modal title</h5>
              <button type="button" class="close" onclick="$('#updateMarginActionModal').modal('hide')">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              @csrf
              <div class="form-group">
                <label for="marginInput">Margin</label>
                <input type="number" class="form-control" id="marginInput" placeholder="Masukkan margin (%)" step="0.01">
                <small id="marginHelp" class="form-text text-muted"></small>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Update margin harga</button>
              <button type="button" class="btn btn-secondary" onclick="$('#updateMarginActionModal').modal('hide')">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body table-responsive">
                <div class="row mb-2">
                    <div class="col-md-12 text-lg-end">
                        <button class="btn btn-warning" id="updateMarginAction">Atur margin harga</button>
                        <a href="{{ $createLink }}" class="btn btn-primary">Tambah data</a>
                    </div>
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th> <input type="checkbox" name="update_all" value="true"/> </th>
                            <th> # </th>
                            <th> Produk </th>
                            <th> Kode </th>
                            <th> Margin </th>
                            <th> Harga </th>
                            <th> Stok </th>
                            <th> Modal </th>
                            <th> Aksi </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productItems as $productItem)
                        <tr>
                            <th> <input type="checkbox" name="product_item_ids" value="{{ $productItem->id }}"/> </th>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $productItem->product->name }} {{ $productItem->name }}</td>
                            <td>{{ $productItem->code }}</td>
                            <td>{{ $productItem->margin_percentage ?? 0 }} %</td>
                            <td>{{ rp_format($productItem->margin_price) }}</td>
                            <td>{{ $productItem->stock }}</td>
                            <td>{{ rp_format($productItem->capital) }}</td>
                            <td>
                                @include('master.action', [
                                    'view_url' => route('product_item.show', $productItem),
                                    'edit_url' => route('product_item.edit', $productItem),
                                    'delete_url' => route('product_item.destroy', $productItem)
                                ])
                                {{-- @if ($productItem->product->category == \App\Constants\ProductConstant::VOUCHER) --}}
                                {{--     <button class="btn btn-gradient-success btn-rounded btn-icon" data-hover="tooltip" title="Kelola Voucher" data-placement="top" --}}
                                {{--        onclick="window.open('{{ route('voucher.index', ['product_item_id' => $productItem->id]) }}', '_blank')"> <i class="mdi mdi-cash"></i> --}}
                                {{--    </button> --}}
                                {{-- @endif --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100%" class="text-center">Tidak ada data ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-2">
                    {!! $productItems->appends(request()->query())->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
  <script>
    document.querySelector('input[name="update_all"]').addEventListener('change', function() {
      document.querySelectorAll('input[name="product_item_ids"]').forEach(function(checkbox) {
        checkbox.checked = this.checked;
      }, this);
    });

    document.querySelector('#updateMarginAction').addEventListener('click', function(event) {
      event.preventDefault();
      const checkboxes = document.querySelectorAll('input[name="product_item_ids"]');
      if (document.querySelectorAll('input[name="product_item_ids"]:checked').length === 0) {
        Swal.fire({
          title: 'Error!',
          text: 'Pilih minimal satu produk item',
          icon: 'error',
        })
      } else {
        $('#updateMarginActionModal').modal('show')
      }
    });

    const form = document.querySelector('#form-update-margin');
    form.addEventListener('submit', function(event) {
      event.preventDefault();
      const margin = document.querySelector('#marginInput').value;
      const productItemIds = document.querySelectorAll('input[name="product_item_ids"]:checked');
      const updateAll = document.querySelector('input[name="update_all"]').checked;
      const data = new FormData();
      data.append('margin', margin);
      if (updateAll) {
        data.append('update_all', updateAll);
      } else {
        productItemIds.forEach(function(checkbox) {
          data.append('product_item_ids[]', checkbox.value);
        });
      }
      fetch('{{ route('product_item_price.store') }}', {
        method: 'POST',
        body: data,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              title: 'Success!',
              text: data.message,
              icon: 'success',
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({
              title: data.message,
              text: data.errors.margin[0],
              icon: 'error',
            });

            this.querySelector('button[type="submit"]').removeAttribute('disabled');
          }
        });
    });
  </script>
@endpush
