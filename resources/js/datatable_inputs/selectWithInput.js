class SelectWithInput {
	constructor(selectorOrElement) {

		this.value = '';
		this.previousValue = '';
		this.previousInputValue = '';

		if (typeof selectorOrElement === 'string') {
			this.select = document.querySelector(selectorOrElement);
		} else {
			this.select = selectorOrElement;
		}

		this.getOptions();
		this.createCustomSelectElement();

		this.hideByOutsideClick = this.hideByOutsideClick.bind(this);
		this.handleInput = this.handleInput.bind(this, this.input);

		this.addListeners();

		Object.defineProperties(this.selectWithInput, {
		    'value': {
		    	get: this.getValue.bind(this),
		    	set: this.setValue.bind(this)
		    },
		});
	}

	getOptions() {
		var selectOptions = Array.prototype.slice.call(this.select.options, 0);

		this.options = selectOptions.map(function(option) {
			return {id: option.value, name: option.innerText}
		});
	}

	createCustomSelectElement() {
		this.selectWithInput = document.createElement('div');
		this.selectWithInput.className = 'select-with-input';
		this.selectWithInput.name = this.select.name;

		this.input = document.createElement('input');
		this.input.className = 'form-control select-with-input__input';
		this.input.name = this.select.name;

		this.showListButton = document.createElement('span');
		this.showListButton.className = 'select-with-input__button';

		this.icon = document.createElement('i');
		this.icon.className = 'select-with-input__button__icon';

		this.showListButton.appendChild(this.icon);

		this.list = document.createElement('div');
		this.list.className = 'select-with-input__list';
		this.list.style.display = 'none';
		this.options.forEach(function(option) {
			var optionItem = document.createElement('div');

			optionItem.className = 'select-with-input__list__option';
			optionItem.dataset.id = option.id;
			optionItem.innerText = option.name;
			if (option.name === '') {
				optionItem.innerHTML = '&nbsp';
			}
			optionItem.value = option.id;
			optionItem.name = option.name;

			optionItem.addEventListener('click', this.handleListOption.bind(this, optionItem));

			this.list.appendChild(optionItem);
		}, this);

		this.selectWithInput.appendChild(this.input);
		this.selectWithInput.appendChild(this.showListButton);
		this.selectWithInput.appendChild(this.list);

		this.select.parentNode.insertBefore(this.selectWithInput, this.select);

		this.select.remove();
	}

	triggerChangeEvent() {
		if (Array.isArray(this.value)) {
			if (this.value.length === this.options.length-1) {
				this.value = '';
			} else if (this.value.length === 1) {
				this.value = this.value[0];
			}
		}

		if (this.value !== this.previousValue) {
			this.previousValue = this.value;

			this.selectWithInput.dispatchEvent(new CustomEvent('change'));
		}
	}

	handleInput(input) {
		if (input.value === this.previousInputValue) return;

		this.previousInputValue = input.value;

		if (input.value !== '') {
			this.value = this.options.reduce(function(result, option) {
				if (option.name.toLowerCase().indexOf(input.value.toLowerCase()) !== -1) {
					result.push(option.id);
				}

				return result;
			}, []);
		} else {
			this.value = '';
		}

		this.triggerChangeEvent();
	}

	handleInputWithDelay() {
		clearTimeout(this.delay);

		this.delay = setTimeout(this.handleInput, 500);
	}

	hideByOutsideClick(event) {
		if (this.list.contains(event.target) || this.showListButton.contains(event.target)) return;
		else this.hideList();
	}

	showList() {
		this.list.style.display = 'block';
		window.addEventListener('mousedown', this.hideByOutsideClick);
	}

	hideList() {
		this.list.style.display = 'none';
		window.removeEventListener('mousedown', this.hideByOutsideClick);
	}

	toggleList() {
		if (this.list.style.display === 'none') this.showList();
		else this.hideList();
	}

	handleShowListButton() {
		this.toggleList();
	}

	handleListOption(optionItem) {
		this.value = optionItem.value;

		this.input.value = optionItem.name;
		this.previousInputValue = optionItem.name;

		this.triggerChangeEvent();
		this.hideList();
	}

	addListeners() {
		this.input.addEventListener('keyup', this.handleInputWithDelay.bind(this));
		this.input.addEventListener('change', this.handleInputWithDelay.bind(this));

		this.showListButton.addEventListener('click', this.handleShowListButton.bind(this));
	}

	getValue() {
		return this.value;
	}

	setValue(value) {
		if (Array.isArray(value)) {
			if (value.length === 0) {
				return this.setValue('');
			} else if (value.length === 1) {
				return this.setValue(value[0]);
			} else {
				value = value.map(function(item){
					return item.toString();
				});

				if (value.length === this.options.length) {
					var searchContainsAllOptions = this.options.every(function(option){
						return value.indexOf(option.id) !== -1;
					});

					if (searchContainsAllOptions) {
						return this.setValue('');
					}
				}

				var optionsNames = this.options.reduce(function(result, option) {
					if (value.indexOf(option.id) !== -1) {
						result.push(option.name);
					}

					return result;
				}, []);

				this.value = value;
				this.input.value = optionsNames.join(', ');
			}
		} else {
			if (value === '') {
				this.value = '';
				this.input.value = '';
			} else {
				value = value.toString();

				var option = this.options.find(function(option) {
					return option.id === value;
				});

				if (option) {
					this.value = option.id;
					this.input.value = option.name;
				}
			}
		}

		this.previousValue = this.value;
		this.previousInputValue = this.input.value;
	}
}

export default SelectWithInput;
