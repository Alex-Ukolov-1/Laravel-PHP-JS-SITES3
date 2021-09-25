@once
<style>
	.filter_customers_by_trips {
		margin: 20px 0 10px 0;
	}

	.filter_customers_by_trips input {
		display: inline-block;
		width: auto;
	}
</style>
@endonce

<div id="filter_customers_by_trips-{{$route}}" class="filter_customers_by_trips">
	<span>Рейсы </span>
	<span>с <input class="form-control" type="date" name="has_trips_date_from" /></span>
	<span>по <input class="form-control" type="date" name="has_trips_date_to" /></span>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		var dataTable = window.dt.{{$route}};

		document.querySelectorAll('#filter_customers_by_trips-{{$route}} input').forEach(function(input){
			var fn = dataTable.change.bind(input, input.name, dataTable);
			input.addEventListener('change', fn);
			input.addEventListener('keyup', fn);
			fn();
		});
	});
</script>