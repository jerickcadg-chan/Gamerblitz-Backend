@if(isset($view_url))
    <a class="btn btn-gradient-info btn-sm" target="_blank" data-bs-toggle="tooltip" title="Detail" data-bs-placement="top"
       href="{{ $view_url }}"> <i class="mdi mdi-magnify menu-icon"></i>
   </a>
@endif
@if(isset($edit_url))
    <button class="btn btn-gradient-warning btn-sm" target="_blank" data-bs-toggle="tooltip" title="Edit" data-bs-placement="top"
    onclick="window.open('{{ $edit_url }}', '_blank')"> <i class="mdi mdi-tooltip-edit menu-icon"></i>
   </button>
@endif
@if(isset($delete_url))
    <form method="POST" action="{{ $delete_url }}"  onsubmit="return confirm('Anda yakin ingin menghapus data ini?');" style="display: inline">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <button type="submit" class="btn btn-gradient-danger btn-sm" data-bs-toggle="tooltip" title="Hapus" data-bs-placement="top"><i class="mdi mdi-delete-forever menu-icon"></i></button>
    </form>
@endif
