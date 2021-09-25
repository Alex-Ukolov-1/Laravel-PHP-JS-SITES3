@if(auth('organization')->check())
  <div class="d-inline-block">
  	<button
  		class="btn btn-warning mr-2"
  		type="submit"
  		formmethod="POST"
  		formaction="{{route($route . '.pay')}}"
  		form="datatable-{{$route}}_form">Оплатить</button>
  </div>
@endif