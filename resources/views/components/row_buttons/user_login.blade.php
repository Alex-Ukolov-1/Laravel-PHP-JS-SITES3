@if(auth('admin')->check())
    <a href="{{ route('auth.login_as_driver', $item->id)}}" class="btn btn-secondary table-icon-btn">&#8594;</a>
@endif