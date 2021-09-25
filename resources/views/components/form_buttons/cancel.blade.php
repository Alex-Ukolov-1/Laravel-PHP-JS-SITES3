<button class="btn btn-secondary mr-1 float-right"
				onclick="event.preventDefault(); location='{{ \request()->server('HTTP_REFERER') ?? route($route . '.index') }}'">Отмена
</button>