@extends('layouts.app', [
  'activePage' => 'account',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Halaman {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('account.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-nospace">
          <tr>
            <th>Nama Akun</th>
            <td>{{ $account->title }}</td>
          </tr>
          <tr>
            <th>Kode</th>
            <td>{{ $account->code }}</td>
          </tr>
          <tr>
            <th>Deskripsi</th>
            <td>{{ $account->description }}</td>
          </tr>
          <tr>
            <th>Winrate</th>
            <td>{{ $account->winrate }}</td>
          </tr>
          <tr>
            <th>Jumlah Skin</th>
            <td>{{ $account->skin }}</td>
          </tr>
          <tr>
            <th>Jumlah Heroes</th>
            <td>{{ $account->heroes }}</td>
          </tr>
          <tr>
            <th>Stok</th>
            <td>{{ $account->productItem->stock }}</td>
          </tr>
          <tr>
            <th>Harga</th>
            <td>{{ rp_format($account->productItem->price) }}</td>
          </tr>
          <tr>
            <th>Informasi</th>
            <td>
              <span id="information-text">xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</span>
              <button type="button" class="btn-sm btn-warning rounded-pill" id="show-information">
                <i class="mdi mdi-eye"></i>
              </button>
              <button type="button" class="btn-sm btn-info rounded-pill" id="edit-information">
                <i class="mdi mdi-pencil"></i>
              </button>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="pinModal" tabindex="-1" role="dialog" aria-labelledby="pinModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="pinModalLabel">Masukkan PIN</h5>
          <button type="button" class="close" onclick="$('#pinModal').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="pinForm">
            <div class="form-group">
              <label for="pinInput">PIN</label>
              <input type="password" class="form-control" id="pinInput" name="pin" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script>
    let editInformation = false;
    document.querySelector('#show-information').addEventListener('click', function(event) {
      editInformation = false;
      $('#pinModal').modal('show')
    });
    document.querySelector('#edit-information').addEventListener('click', function(event) {
      editInformation = true;
      $('#pinModal').modal('show')
    });
    document.getElementById('pinForm').addEventListener('submit', function(event) {
      event.preventDefault();
      const pin = document.getElementById('pinInput').value;
      fetch('{{ route('account.show-information', $account) }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ pin: pin })
      })
        .then(response => response.json())
        .then(data => {
          this.querySelector('button[type="submit"]').removeAttribute('disabled');
          if (editInformation) {
            window.location.href = '{{ route('account.edit', $account) }}?edit-information=' +pin
          } else {
            document.getElementById('information-text').innerText = data.data;
            Swal.fire({
              title: "Sukses membuka informasi",
              icon: 'success',
            });
          }
          $('#pinModal').modal('hide')
        })
        .catch(error => {
          this.querySelector('button[type="submit"]').removeAttribute('disabled');
          console.log(error);
          Swal.fire({
            title: "Pin Salah",
            icon: 'error',
          });
        });
    });
  </script>
@endpush

