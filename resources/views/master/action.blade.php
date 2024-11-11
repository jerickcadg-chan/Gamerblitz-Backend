@if(isset($view_url))
    <a class="btn btn-gradient-info btn-sm" data-hover="tooltip" title="Detail" data-placement="top"
       href="{{ $view_url }}"> <i class="mdi mdi-magnify menu-icon"></i>
   </a>
@endif
@if(isset($edit_url))
    <button class="btn btn-gradient-warning btn-sm" data-hover="tooltip" title="Edit" data-placement="top"
       onclick="window.location.href='{{ $edit_url }}'"> <i class="mdi mdi-tooltip-edit menu-icon"></i>
   </button>
@endif
@if(isset($delete_url))
    <form method="POST" action="{{ $delete_url }}"  onsubmit="return confirm('Anda yakin ingin menghapus data ini?');" style="display: inline">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <button type="submit" class="btn btn-gradient-danger btn-sm" data-hover="tooltip" title="Hapus" data-placement="top"><i class="mdi mdi-delete-forever menu-icon"></i></button>
    </form>
@endif
