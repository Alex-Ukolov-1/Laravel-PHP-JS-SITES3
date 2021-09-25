class ModalAlert {
	constructor(type) {
		var iconClass;

		this.hide = this.hide.bind(this);

		this.div = document.createElement('div');
		this.div.className = 'alert alert--modal alert-'+type;

		if (type === 'success') {
			iconClass = 'far fa-check-circle';
		} else if (type === 'error') {
			iconClass = 'far fa-times-circle';
		} else if (type === 'warning') {
			iconClass = 'fas fa-exclamation-circle';
		} else if (type === 'wait') {
			iconClass = 'far fa-clock';
		} else if (type === 'info') {
			iconClass = 'fas fa-info-circle';
		}

		this.i = document.createElement('i');
		this.i.className = iconClass;

		this.span = document.createElement('span');
		this.span.className = 'alert__text';

		this.div.appendChild(this.i);
		this.div.appendChild(this.span);
	}

	setText(text) {
		this.span.innerText = text;
	}

	show() {
		document.body.appendChild(this.div);
	}

	hide() {
		this.div.remove();
	}
}

var Alert = {
	alerts: [],

	showAlert: function(type, text) {
		if (this.alerts[type] === undefined) {
			this.alerts[type] = new ModalAlert(type);
		}

		this.alerts[type].setText(text);
		this.alerts[type].show();

		if (type !== 'wait') {
			setTimeout(this.alerts[type].hide, 3000);
		}
	},

	hideWait: function() {
		this.alerts['wait'].hide();
	},

	success: function(text) {
		this.showAlert('success', text);
	},

	error: function(text) {
		this.showAlert('error', text);
	},

	warning: function(text) {
		this.showAlert('warning', text);
	},

	wait: function(text) {
		this.showAlert('wait', text);
	},

	info: function(text) {
		this.showAlert('info', text);
	},
}

export default Alert;
