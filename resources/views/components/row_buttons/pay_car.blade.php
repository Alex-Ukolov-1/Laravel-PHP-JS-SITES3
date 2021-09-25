@if(auth('organization')->check())
	<a class="btn btn-warning" href="{{ route($route . '.pay', 'ids[]='.$item->id)}}">
		@if ($item->is_paid)
			Продлить
		@else
			Оплатить
		@endif
	</a>
@endif