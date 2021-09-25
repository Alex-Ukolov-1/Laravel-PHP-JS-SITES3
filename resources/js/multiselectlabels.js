var MultiSelectLabels = function(elementOrSelector, data) {

	if (typeof elementOrSelector === 'string') {
		this.elem = document.querySelector(elementOrSelector);
	} else {
		this.elem = elementOrSelector;
	}

	this.name = this.elem.dataset.name;

	var emptyInput = this.createInput('');
			emptyInput.name = this.name;

	this.elem.appendChild(emptyInput);

	this.selectedList = this.elem.querySelector('.selected');
	this.select = this.elem.querySelector('select');

	if (!this.selectedList) {
		var div = document.createElement('div');
		div.className = 'selected';
		this.elem.appendChild(div);
		this.selectedList = div;
	}

	if (!this.select) {
		var divInputGroup = document.createElement('div');
				divInputGroup.className = 'input-group';
		
		var divInputGroupPrepend = document.createElement('div');
				divInputGroupPrepend.className = 'input-group-prepend';

		var spanInputGroupText = document.createElement('span');
				spanInputGroupText.className = 'input-group-text';
				spanInputGroupText.textContent = 'Добавить:';

		var select = document.createElement('select');
		var option = document.createElement('option');
		
		select.className = 'form-control';

		option.value = '';
		option.style.display = 'none';

		select.appendChild(option);

		divInputGroupPrepend.appendChild(spanInputGroupText);
		divInputGroup.appendChild(divInputGroupPrepend);
		divInputGroup.appendChild(select);

		this.elem.appendChild(divInputGroup);
		this.select = select;
	}

	if (typeof data !== 'undefined') {
		if (typeof data === 'string') data = JSON.parse(data);
		var key;

		for (key in data) {
			this.select.appendChild(this.createOption(key, data[key]));
		}
	}

	if (this.elem.dataset.value) {
		var initValue = this.elem.dataset.value.split(',');

		for (var i = 0; i < initValue.length; i++) {
			this.select.value = initValue[i];
			this.addSelected();
		}
	}

	this.select.addEventListener('change', this.addSelected.bind(this));
}

MultiSelectLabels.prototype = {
	constructor: MultiSelectLabels,

	createOption: function(value, text) {
		var option = document.createElement('option');
		option.value = value;
		option.textContent = text;
		return option;
	},

	createInput: function(value) {
		var input = document.createElement('input');

		input.type = 'hidden';
		input.name = this.name + '[]';
		input.value = value;
		input.style.display = 'none';

		return input;
	},

	addSelected: function() {
		var value = this.select.value;
		var text = this.select.options[this.select.selectedIndex].textContent;

		this.select.value = '';

		this.addSelectedRow(value, text);
	},

	addSelectedRow: function(value, name) {
		var div = document.createElement('div');
				div.className = 'mt-1 mb-2';

		var text = document.createTextNode(name);
		var span = document.createElement('span');
		var x = document.createTextNode('✕');

		span.addEventListener('click', div.remove.bind(div));

		span.appendChild(x);
		div.appendChild(text);
		div.appendChild(span);

		if (this.name) {
			div.appendChild(this.createInput(value));
		}

		this.selectedList.appendChild(div);
	},
}

export default MultiSelectLabels;
