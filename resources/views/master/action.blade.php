@if(isset($view_url))
    <a class="btn btn-gradient-info btn-sm" data-bs-toggle="tooltip" title="Detail" data-bs-placement="top"
       href="{{ $view_url }}"> <i class="mdi mdi-magnify menu-icon"></i>
   </a>
@endif
@if(isset($edit_url))
    <a class="btn btn-gradient-warning btn-sm" data-bs-toggle="tooltip" title="Edit" data-bs-placement="top"
       href="{{ $edit_url }}"> <i class="mdi mdi-tooltip-edit menu-icon"></i>
   </a>
@endif
@if(isset($delete_url))
    <form method="POST" action="{{ $delete_url }}"  onsubmit="return confirm('Are you sure to delete this data?');" style="display: inline">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <button type="submit" class="btn btn-gradient-danger btn-sm" data-bs-toggle="tooltip" title="Delete" data-bs-placement="top"><i class="mdi mdi-delete-forever menu-icon"></i></button>
    </form>
@endif
