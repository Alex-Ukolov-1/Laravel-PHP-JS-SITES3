var inputTethering = function(el, event, delimiter) {
	if (delimiter === undefined) delimiter = ' ';

	var value = [];

	document.querySelectorAll('[data-name="'+el.dataset.name+'"]').forEach(function(input){
		value.push(input.value);
	});

	document.querySelector('[name="'+el.dataset.name+'"]').value = value.join(delimiter);
}

export default inputTethering;