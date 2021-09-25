@once
<style>
	.sum_of_money_and_cargo {
		margin: 17px 0 12px 0;
	}
</style>
@endonce

<div id="sum_of_money_and_cargo-{{$route}}" class="sum_of_money_and_cargo">
	<span style="font-weight: bold;">Итоговая сумма: </span><span class="sum_of_money"></span>
	<span style="margin-right: 16px;"></span>
	<span style="font-weight: bold;">Итоговое кол.груза: </span><span class="sum_of_cargo"></span>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		var dataTable = window.dt.{{$route}};

		var pluginEl = document.getElementById('sum_of_money_and_cargo-{{$route}}');

		var sumOfMoneyEl = pluginEl.querySelector('.sum_of_money');
		var sumOfCargoEl = pluginEl.querySelector('.sum_of_cargo');

		var numberFormat = new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

		dataTable.addEventListener('update', function() {
			sumOfMoneyEl.innerText = numberFormat.format(dataTable.data.sum_of_money);
			sumOfCargoEl.innerText = numberFormat.format(dataTable.data.sum_of_cargo);
		});
	});
</script>