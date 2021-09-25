(function() {

function toFloat(value) {
	return parseFloat(value.toFixed(2));
}

var Profitability = function(form) {
	this.fieldWasChanged = this.fieldWasChanged.bind(this);

	this.numberFormat = new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2 });

	this.createResultElement();

	this.form = form;

	form.addEventListener('keyup', this.fieldWasChanged);
	form.addEventListener('change', this.fieldWasChanged);

	this.init();
}

Profitability.prototype = {
	constructor: Profitability,

	init: function() {
		var formElements = Array.prototype.slice.call(this.form.elements);

		formElements.forEach(function(el){
			if (el.nodeName === 'BUTTON' || el.tagName === 'BUTTON') return;
			if (el.nodeName === 'TEXTAREA' || el.tagName === 'TEXTAREA') return;
			if (el.type === 'radio' && el.checked === false) return;

			this[el.name] = parseFloat(el.value);
		}, this);

		this.fullRecalc();
	},

	createResultElement: function() {
		this.resultsEl = document.createElement('div');

		this.resultsEl.setAttribute('id', 'profitability-modal');

		document.querySelector('body').appendChild(this.resultsEl);
	},

	fieldWasChanged: function(event) {
		if (this[event.target.name] === parseFloat(event.target.value)) return;

		this[event.target.name] = parseFloat(event.target.value);
		this.fullRecalc();
	},

	recalcMain: function() {
		if (isNaN(this.conversion_factor) || this.conversion_factor === '') {
			this.unloadingVolume = undefined;
			this.unloadingPrice = undefined;
			this.loadingPrice = undefined;
			this.loadingVolume = undefined;

			return;
		}

		this.unloadingVolume = this.unloading_volume; // Объём отгрузки заказчику за рейс (грузоподъёмность ТС)
		this.unloadingPrice = this.unloading_price; // Выручка за доставку единицы груза
		this.loadingVolume = this.loading_volume; // Объём приобретённого груза за рейс
		this.loadingPrice = this.loading_price; // Цена покупки единицы груза

		if (this.unloading_volume_type_id === 1) this.unloadingVolume = toFloat(this.unloadingVolume / this.conversion_factor);
		if (this.unloading_price_type_id === 1) this.unloadingPrice = toFloat(this.unloadingPrice / this.conversion_factor);
		if (this.loading_price_type_id === 1) this.loadingPrice = toFloat(this.loadingPrice / this.conversion_factor);
		if (this.loading_volume_type_id === 1) this.loadingVolume = toFloat(this.loadingVolume / this.conversion_factor);
	},

	calcIntermediateResults: function() {
		// Общий пробег за рейс
		if (this.trip_direction_id === 1) {
			this.fullTripDistance = toFloat(this.distance * 2);
		} else {
			this.fullTripDistance = this.distance;
		}

		this.revenueForTheTrip = toFloat(this.unloadingVolume * this.unloadingPrice); // Выручка за рейс
		this.costOfTheCargo = toFloat(this.loadingVolume * this.loadingPrice); // Затраты на покупку груза за рейс

		// Заработная плата водителя ТС за рейс
		if (this.driver_salary_type_id === 2) { // Заработная плата водителя за 1 км
			if (this.driver_salary_direction_id === 1) {
				this.driversSalaryForTheTrip = toFloat(this.fullTripDistance * this.driver_salary / 2);
			} else {
				this.driversSalaryForTheTrip = toFloat(this.fullTripDistance * this.driver_salary);
			}
		} else if (this.driver_salary_type_id === 1) { // Заработная плата водителя за рейс
			this.driversSalaryForTheTrip = this.driver_salary;
		} else if (this.driver_salary_type_id === 3) { // Заработная плата водителя за ед. груза при разгрузке
			this.driversSalaryForTheTrip = toFloat(this.driver_salary * this.unloading_volume);
		}

        this.driversSalaryForTheTrip = this.driver_salary;

		this.priceOfFuelForTheTrip = toFloat(this.price_of_fuel * this.fullTripDistance * this.average_fuel_consumption / 100); // Цена топлива за рейс
		this.fixedOverhead = this.fixed_overhead; // Постоянные накладные расходы на рейс
		this.additionalOverhead = this.additional_overhead; // Дополнительные накдалные расходы на рейс
	},

	calcResults: function() {
		this.revenueForTheTripWithoutVAT = this.revenueForTheTrip;
		this.costOfTheCargoWithoutVAT = this.costOfTheCargo;
		this.driversSalaryForTheTripWithoutVAT = this.driversSalaryForTheTrip;
		this.priceOfFuelForTheTripWithoutVAT = this.priceOfFuelForTheTrip;
		this.fixedOverheadWithoutVAT = this.fixedOverhead;
		this.additionalOverheadWithoutVAT = this.additionalOverhead;

		if (this.vat_in_income === 1) this.revenueForTheTrip = toFloat(this.revenueForTheTrip / 1.2);
		if (this.vat_in_cargo_expenses === 1) this.costOfTheCargo = toFloat(this.costOfTheCargo / 1.2);
		if (this.with_taxes === 1) this.driversSalaryForTheTrip = toFloat(this.driversSalaryForTheTrip * 1.432);
		if (this.vat_in_fuel_expenses === 1) this.priceOfFuelForTheTrip = toFloat(this.priceOfFuelForTheTrip / 1.2);
		if (this.vat_in_fixed_overhead === 1) this.fixedOverhead = toFloat(this.fixedOverhead / 1.2);
		if (this.vat_in_additional_overhead === 1) this.additionalOverhead = toFloat(this.additionalOverhead / 1.2);

		this.shippingCost = toFloat(((this.revenueForTheTrip / this.unloadingVolume) - (this.costOfTheCargo / this.unloadingVolume)) / this.distance);
		this.profit = toFloat(this.revenueForTheTrip - (this.costOfTheCargo  + this.driversSalaryForTheTrip +  this.priceOfFuelForTheTrip + this.additionalOverhead));
	},

    generateName: function () {
        let generateName = $('input[name=name]').attr('value');
        if (this.distance) generateName += '-' + this.distance;
        if (this.unloading_price) generateName += '-' + this.unloading_price;
        $('input[name=name]').val(generateName);
    },

	format: function(value, unit) {
		return (value ? this.numberFormat.format(value) : '-') + ' ' + (unit || 'руб.');
	},

	renderResults: function() {
		this.resultsEl.innerHTML = "\
		   <table>\
		     <tr>\
			    <td>Общий пробег за рейс: </td><td>" + this.format(this.fullTripDistance, 'км') + "</td>\
			 </tr>\
			 <tr>\
			    <td>Выручка за рейс: </td><td>" + this.format(this.revenueForTheTripWithoutVAT) + "</td>\
			 </tr>\
			 <tr>\
			    <td>Затраты на покупку груза за рейс: </td><td>" + this.format(this.costOfTheCargoWithoutVAT) + "</td>\
			 </tr>\
             <tr style='display: none'>\
			    <td>Заработная плата водителя ТС за рейс: </td><td>" + this.format(this.driversSalaryForTheTripWithoutVAT) + "</td>\
			 </tr>\
			 <tr>\
			    <td>Цена топлива за рейс: </td><td>" + this.format(this.priceOfFuelForTheTripWithoutVAT) + "</td>\
			 </tr>\
			 <tr>\
			    <td>Доп. накладные расходы на рейс: </td><td>" + this.format(this.additionalOverheadWithoutVAT) + "</td>\
			 </tr>\
			 <tr><td>&nbsp;</td><td>&nbsp;</td></tr>\
			 <tr>\
			    <td>Цена доставки: </td><td>" + this.format(this.shippingCost, 'куб.м/км') + "</td>\
			 </tr>\
			 <tr>\
			    <td>Итого прибыль за рейс: </td><td>" + this.format(this.profit) + "</td>\
			 </tr>\
			 </table>";
	},

	fullRecalc: function() {
		this.recalcMain();
		this.calcIntermediateResults();
		this.calcResults();
		this.generateName();
		this.renderResults();
	},
}

var form = document.querySelector('form.create_edit_form');

new Profitability(form);
})();

$(document).ready(function(){
    $('.without_profitability').click(function(){
        $.ajax({
            url: "/contract_without_profitability",
            data: $('.create_edit_form').serialize(),
            success: function (data) {
                alert("Заказ успешно создан");
            },
            error: function(data){
                console.log('error');
            }
        });
    });
});
