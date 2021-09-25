class Modal {
	constructor(title, body, footer) {
		this.hide = this.hide.bind(this);
		this.handlePressESC = this.handlePressESC.bind(this);

		var _this = this;

		var modal = document.createElement('div');
			modal.className = 'xmodal xmodal--hidden';
			modal.addEventListener('click', function(event){
				if (event.target === modal) _this.hide();
			});

		var modalContent = document.createElement('div');
			modalContent.className = 'xmodal__content';

		var modalContentHeader = document.createElement('div');
			modalContentHeader.className = 'xmodal__content__header';

		var modalContentHeaderTitle = document.createElement('h4');
			modalContentHeaderTitle.className = 'xmodal__content__header__title';
		if (title !== undefined) {
			modalContentHeaderTitle.innerHTML = title;
		}

		var modalContentHeaderClose = document.createElement('button');
			modalContentHeaderClose.className = 'xmodal__content__header__close';
			modalContentHeaderClose.type = 'button';
			modalContentHeaderClose.addEventListener('click', this.hide);

		var modalContentHeaderCloseSpan = document.createElement('span');
			modalContentHeaderCloseSpan.innerHTML = '&times;';

		var modalContentBody = document.createElement('div');
			modalContentBody.className = 'xmodal__content__body';
		if (body !== undefined) {
			modalContentBody.innerHTML = body;
		}

		var modalContentFooter = document.createElement('div');
			modalContentFooter.className = 'xmodal__content__footer';
		if (footer !== undefined) {
			modalContentFooter.innerHTML = footer;
		}

		modal.appendChild(modalContent);
		modalContent.appendChild(modalContentHeader);
		modalContent.appendChild(modalContentBody);
		modalContent.appendChild(modalContentFooter);
		modalContentHeader.appendChild(modalContentHeaderTitle);
		modalContentHeader.appendChild(modalContentHeaderClose);
		modalContentHeaderClose.appendChild(modalContentHeaderCloseSpan);

		this.modal = modal;
		this.title = modalContentHeaderTitle;
		this.body = modalContentBody;
		this.footer = modalContentFooter;
	}

	setTitle(content) {
		this.title.innerHTML = content;
	}

	setBody(content) {
		this.body.innerHTML = content;
	}

	setFooter(content) {
		this.footer.innerHTML = content;
	}

	append() {
		document.body.appendChild(this.modal);
	}

	remove() {
		this.modal.remove();
	}

	handlePressESC(event) {
		if (event.keyCode === 27 || event.which === 27 || event.key === 'Escape') {
			this.hide();
		}
	}

	show() {
		window.addEventListener('keydown', this.handlePressESC);

		document.body.style.overflow = 'hidden';
		this.append();
		this.modal.classList.remove('xmodal--hidden');
		this.modal.classList.add('xmodal--visible');
	}

	hide() {
		window.removeEventListener('keydown', this.handlePressESC);

		this.modal.classList.remove('xmodal--visible');
		this.modal.classList.add('xmodal--hidden');
		this.remove();
		document.body.style.overflow = '';
	}
}

export default Modal;
