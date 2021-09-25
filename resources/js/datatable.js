class DataTableCache {
	constructor() {
		this.storage = {};
	}

	set(hash, data) {
		this.storage[hash] = data;
	}

	get(hash) {
		return this.storage[hash];
	}

	delete(hash) {
		delete this.storage[hash];
	}

	flush() {
		this.storage = {};
	}

	isExists(hash) {
		return (typeof this.storage[hash] !== 'undefined');
	}
}

class DataTable extends EventTarget {
	constructor(tableSelector) {
		super();

		this.filters = {};
		this.cache = new DataTableCache;

		this.tableWrapper = document.querySelector(tableSelector);

		this.sort = this.tableWrapper.dataset.sort || 'id,asc';

		this.sendRequest = this.sendRequest.bind(this);
		this.handleResponse = this.handleResponse.bind(this);

		this.request = new XMLHttpRequest();
		this.request.addEventListener('load', this.handleResponse);

		this.token = document.querySelector('meta[name="csrf-token"]').content;

		this.tbody = this.tableWrapper.querySelector('tbody');
		this.perPageSelector = this.tableWrapper.querySelector('.per_page-selector');
		// this.pageSelector = this.tableWrapper.querySelector('.page-selector');

		this.route = window.location.protocol + '//' + window.location.host + '/' + this.tableWrapper.dataset.route;
		this.url = this.route + '/filter' + window.location.search;

		this.filtersFromUrl = this.getFiltersFromUrl();

		if (this.shouldLoadState()) {
			this.loadStateFromSessionStorage();
		}

		this.tableWrapper.querySelectorAll('th > input, th > select, th > .select-with-input, th > .date-range, .pagination input, .pagination select').forEach(function(input) {
			if (this.filtersFromUrl[input.name] !== undefined) {
				input.value = this.filtersFromUrl[input.name];
				this.setFilter(input.name, this.filtersFromUrl[input.name], input.dataset.strict === 'false' ? false : true, input.type);
			}

			if (this.filters[input.name] !== undefined) {
				input.value = this.filters[input.name].value;
				this.setFilter(input.name, this.filters[input.name].value, this.filters[input.name].strict, this.filters[input.name].type);
			}

			var fn = this.change.bind(input, input.name, this);
			input.addEventListener('change', fn);
			input.addEventListener('keyup', fn);
			// fn();
		}, this);

	    this.tableWrapper.querySelectorAll('svg').forEach(function(icon){
	        var fn = this.orderBy.bind(icon, icon, this);
	        icon.addEventListener('click', fn);
	    }, this);

	    this.sendRequest();
	}

	shouldLoadState() {
		// Если пользователь попал на страницу через "назад" или "вперёд" в браузере
		if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
			return true;
		}

		if (document.referrer !== '') {
			var referrer = new URL(document.referrer);

			// Если пользователь попал на страницу с этой же самой страницы, то есть, например, снова нажал в меню на данный раздел
			if (window.location.origin + window.location.pathname === referrer.origin + referrer.pathname) {
				return false;
			}

			// Если пользователь попал на страницу с какой-то подстраницы текущего раздела, например, из создания или редактирования записи
			if (referrer.pathname.indexOf(window.location.pathname) !== -1) {
				return true;
			}
		}

		return false;
	}

	saveStateToSessionStorage() {
		var currentState = {
			filters: this.filters,
			page: this.page,
			sort: this.sort,
		};

		sessionStorage.setItem(this.tableWrapper.dataset.route, JSON.stringify(currentState));
	}

	loadStateFromSessionStorage() {
		var loadedState = sessionStorage.getItem(this.tableWrapper.dataset.route);

		if (loadedState !== null) {
			loadedState = JSON.parse(loadedState);

			this.filters = loadedState.filters;
			this.page = loadedState.page;
			this.sort = loadedState.sort;

			this.fillCurrentSortArrow();
		}
	}

	forceRefresh() {
		this.cache.flush();
		this.sendRequest();
	}

	getFiltersFromUrl() {
		var hash, filters, result = {};
		
		hash = window.location.hash.substr(1);

		if (hash !== '') {
			filters = hash.split('&');
			filters.forEach(function(filter){
				var splitted = filter.split('=');
				result[splitted[0]] = splitted[1];
			});
		}

		return result;
	}

    orderBy(icon, _this) {
	    var field = icon.dataset.name;
	    var order = icon.dataset.order;

	    if (order === 'DESC') order = 'ASC'; else order = 'DESC';

	    _this.tableWrapper.querySelectorAll('.sort-arrow').forEach(function (arrow) {
	        arrow.removeAttribute('fill');
	    });

        icon.querySelectorAll('.sort-arrow-' + order.toLowerCase()).forEach(function (arrow) {
            arrow.setAttribute('fill', '#FF0000');
        });

        icon.dataset.order = order;

	    _this.sort = field + ',' + order;

	    _this.resetPageNumber();
        _this.sendRequest();
    }

    fillCurrentSortArrow() {
    	this.tableWrapper.querySelectorAll('.sort-arrow').forEach(function (arrow) {
    	    arrow.removeAttribute('fill');
    	});

    	var field = this.sort.split(',')[0];
    	var order = this.sort.split(',')[1];

    	this.tableWrapper.querySelectorAll('svg[data-name="'+field+'"] .sort-arrow-' + order.toLowerCase()).forEach(function (arrow) {
    	    arrow.setAttribute('fill', '#FF0000');
    	});
    }

    resetPageNumber() {
    	this.filters.page = 1;
    }

    setFilter(name, value, strict, type) {
    	value = typeof value === 'string' ? value.trim() : value;

    	if (value !== '') {
    		this.filters[name] = {
    			value: value,
    			strict: strict,
    		}

    		if (type === 'date') {
    			this.filters[name]['type'] = 'date';
    		}
    	} else {
    		delete this.filters[name];
    	}
    }

	change(name, _this) {
		_this.resetPageNumber();

		var strict = this.dataset.strict === 'false' ? false : true;

		_this.setFilter(name, this.value, strict, this.type);

		clearTimeout(this.delay);

		if (this.tagName === 'SELECT' || this.tagName === 'DIV') {
			_this.sendRequest();
		} else {
			this.delay = setTimeout(_this.sendRequest, 500);
		}
	}

	hashString(str) {
		var hash = 0, i, chr;

		if (str.length === 0) return hash;

		for (i = 0; i < str.length; i++) {
			chr   = str.charCodeAt(i);
			hash  = ((hash << 5) - hash) + chr;
			hash |= 0;
		}

		return hash;
	}

	prepareRequestData() {
		var index;

		this.prevHash = this.hash;

		this.filterKeys = Object.keys(this.filters).sort();

		this.requestData = {};

		this.requestData['sort'] = this.sort;

		for (index in this.filterKeys) {
			this.requestData[this.filterKeys[index]] = this.filters[this.filterKeys[index]];
		}

		this.hash = this.hashString(JSON.stringify(this.requestData));
	}

	setURLHash() {
		var filterName, URLHash = '';

		for (filterName in this.filters) {
			if (typeof this.filters[filterName] !== 'object') continue;
			URLHash += filterName + '=' + this.filters[filterName].value + '&';
		}

		window.location.hash = '#' + URLHash.slice(0, -1);
	}

	renderData(data) {
		this.data = data;

		this.dispatchEvent(new Event('update'));

        // table
	    this.tbody.innerHTML = data.table;

        var onPage = (data.table.match(/<tr>/g) || []).length;
        this.handlePageStatsBlock(data.pages, data.page, data.per_page, data.total, onPage);

        // this.pageSelector.length = data.pages;

        // if (this.pageSelector.length < data.pages) {
        //     Array.apply(undefined, this.pageSelector.children).forEach(function(option, index){
        //         option.value = index+1;
        //         option.innerText = index+1;
        //     });
        // }

        this.perPageSelector.value = data.per_page;
        // this.pageSelector.value = data.page;
	}

    handlePageStatsBlock(pages, page, per_page, total, onPage) {
        $(".total")[0].innerText = total;

        if (pages == 1) {
            $(".lowest")[0].innerText = 1;
            $(".highest")[0].innerText = total;
            return;
        }

        if (page == pages) {
            $(".lowest")[0].innerText = total - onPage;
            $(".highest")[0].innerText = total;
            return;
        }

        if (page == 1) {
            $(".lowest")[0].innerText = 1;
            $(".highest")[0].innerText = onPage;
        } else {
            $(".lowest")[0].innerText = per_page*page - onPage;
            $(".highest")[0].innerText = per_page*page;
        }
    }

	handleResponse(event) {
		if (event.target.status >= 200 && event.target.status < 400) {
			var data = JSON.parse(event.target.responseText);

            $('.datatable-pagination').pagination({
                pages: data.pages,
                currentPage: data.page,
                prevText: 'Назад',
                nextText: 'Вперёд',
                onPageClick: (pageNumber) => {
                    this.filters.page = pageNumber
                    this.sendRequest();
                },
                cssStyle: 'compact-theme'
            });

			this.renderData(data);

			Alert.hideWait();

            this.cache.set(data.hash, data);
		} else {
			Alert.hideWait();
			Alert.error('Ошибка');
		}
	}

	sendRequest() {
		this.prepareRequestData();
		this.saveStateToSessionStorage();

		// Если hash не изменился
		if (this.hash === this.prevHash) return;

		// Если такой hash уже был и он есть в кэше
		if (this.cache.isExists(this.hash)) {
			this.renderData(this.cache.get(this.hash));
			return;
		}

		this.requestData['hash'] = this.hash;

		this.request.open('POST', this.url, true);

		this.request.setRequestHeader('Content-Type', "application/json; charset=UTF-8");
		this.request.setRequestHeader('X-CSRF-TOKEN', this.token);

		Alert.wait('Загрузка...');
		this.request.send(JSON.stringify(this.requestData));
	}
}

export default DataTable;
