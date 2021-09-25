var Mask = {
	float: function(input, event) {
		var value = input.value.replace(/,/g, '.').replace(/[^\d.]/g, '').split('.');
		if (value.length >= 2) {
			value.length = 2;
			value[1] = value[1].substring(0, 2);
		}
		input.value = value.join('.');
	},

	phone: function(input, event) {

	},

	onlyDigits: function(input, event) {
		if (event.key === undefined || event.key === null) return;

		if (event.key !== '.' && event.key !== ',' && isNaN(parseInt(event.key))) {
			event.preventDefault();
		}
	},
};

export default Mask;