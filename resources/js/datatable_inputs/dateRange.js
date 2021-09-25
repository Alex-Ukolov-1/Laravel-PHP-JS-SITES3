class DateRange {
	constructor(selectorOrElement) {
		this.handleChange = this.handleChange.bind(this);
		this.handleChangeWithDelay = this.handleChangeWithDelay.bind(this);

		this.value = '';

		if (typeof selectorOrElement === 'string') {
			this.input = document.querySelector(selectorOrElement);
		} else {
			this.input = selectorOrElement;
		}

		this.createDateRangeElement();

		Object.defineProperties(this.dateRange, {
		    'value': {
		    	get: this.getValue.bind(this),
		    	set: this.setValue.bind(this)
		    },
		});
	}

	getValue() {
		return this.value;
	}

	setValue(value) {
		if (!Array.isArray(value) && value !== '') return;

		if (Array.isArray(value)) {
			this.inputFrom.value = value[0];
			this.inputTo.value = value[1];
		} else if (value === '') {
			this.inputFrom.value = '';
			this.inputTo.value = '';
		}

		this.value = [this.inputFrom.value, this.inputTo.value];

		if (this.value[0] === '' && this.value[1] === '') {
			this.value = '';
		}
	}

	createDateRangeElement() {
		this.dateRange = document.createElement('div');

		this.dateRange.type = this.input.type
		this.dateRange.name = this.input.name
		this.dateRange.className = 'date-range';

		this.dateRange.appendChild(this.createDateFrom());
		this.dateRange.appendChild(this.createDateTo());

		this.input.parentNode.insertBefore(this.dateRange, this.input);

		this.input.remove();
	}

	createInput() {
		var div, span, input;
		
		div = document.createElement('div');
		div.className = 'date-range__wrapper';

		span = document.createElement('span');
		span.className = 'date-range__wrapper__span';

		input = document.createElement('input');
		input.type = 'date';
		input.className = 'form-control form-control-sm date-range__wrapper__input';

		input.addEventListener('keyup', this.handleChangeWithDelay);
		input.addEventListener('change', this.handleChangeWithDelay);

		div.appendChild(span);
		div.appendChild(input);

		return div;
	}

	createDateFrom() {
		var div = this.createInput();

		div.querySelector('span').innerHTML = 'с&nbsp;';

		this.inputFrom = div.querySelector('input');

		return div;
	}

	createDateTo() {
		var div = this.createInput();

		div.style.marginTop = '5px';

		div.querySelector('span').innerHTML = 'по&nbsp;';

		this.inputTo = div.querySelector('input');

		return div;
	}

	triggerChangeEvent() {
		this.dateRange.dispatchEvent(new CustomEvent('change'));
	}

	handleChange() {
		this.value = [this.inputFrom.value, this.inputTo.value];

		if (this.value[0] === '' && this.value[1] === '') {
			this.value = '';
		}

		this.triggerChangeEvent();
	}

	handleChangeWithDelay() {
		clearTimeout(this.delay);

		this.delay = setTimeout(this.handleChange, 500);
	}
}

export default DateRange;
