@if(!auth('driver')->check())
  <form action="{{ route($route . '.destroy', $item->id)}}" onsubmit="dt_rmv(event);" method="post">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger table-icon-btn" type="submit"><i class="fas fa-times"></i></button>
  </form>
@endif