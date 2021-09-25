require('./polyfills/NodeListForEach.js');
require('./polyfills/Closest.js');
// require('jquery/src/jquery');
require('./bootstrap.js');
require('./autocomplete/form');

import DataTable from './datatable';
window.DataTable = DataTable;

import MultiSelectLabels from './multiselectlabels';
window.MultiSelectLabels = MultiSelectLabels;

import FileTable from "./filetable";
window.FileTable = FileTable;

import Mask from './mask';
window.Mask = Mask;

import Form from './autocomplete/form';
window.Form = Form;

import Modal from './autocomplete/modal';
window.Modal = Modal;

import inputTethering from './helpers/inputTethering';
window.inputTethering = inputTethering;

import SelectWithInput from './datatable_inputs/selectWithInput';
window.SelectWithInput = SelectWithInput;

import DateRange from './datatable_inputs/dateRange';
window.DateRange = DateRange;

import Alert from './alert';
window.Alert = Alert;

import xModal from './modal';
window.xModal = xModal;

import Cookies from 'js-cookie';

window.selectAllRows = function(tableId) {
    document.querySelectorAll('#' + tableId + ' tbody input[type="checkbox"]').forEach(function(checkbox){
        checkbox.checked = true;
    });
}

window.deselectAllRows = function(tableId) {
    document.querySelectorAll('#' + tableId + ' tbody input[type="checkbox"]').forEach(function(checkbox){
        checkbox.checked = false;
    });
}

window.dt_rmv_selected = function() {
    let selectedRows = [];

    $('td input:checked').each(function() {
        selectedRows.push($(this));
    });

    selectedRows.forEach(function(el) {
        const rowDeleteForm = $(el[0].parentElement.parentElement).find('form')[0];
        dt_rmv(null, true, rowDeleteForm);
    });
}

window.dt_rmv = function(e, forceDelete = false, elementForm = null) {
    if (e) {
        e.preventDefault();
    }

    if ( !forceDelete ) {
        if (confirm('Данные будут безвозвратно удалены') === false) return;
    }

    if( !elementForm ) {
        var form = e.target;
    } else {
        var form = elementForm;
    }

    var row = form.parentElement.parentElement;
    var rowParent = row.parentElement;
    var nextRow = row.nextElementSibling;

    row.remove();

    var request = new XMLHttpRequest();
    request.open(form.method, form.action, true);

    request.onload = function() {
        if (this.status !== 200) {
            rowParent.insertBefore(row, nextRow);

            Alert.error('Ошибка');
        } else {
            var response, modal;

            response = JSON.parse(this.response);

            if (response.status === 'error') {
                rowParent.insertBefore(row, nextRow);

                modal = new xModal(response.error, response.msg);
                modal.show();
            } else {
                Alert.success('Удалено!');
            }
        }
    }

    request.send(new FormData(form));
}

window.deleteButtonOnShowPage = function(event) {
    event.preventDefault();

    if (confirm('Данные будут безвозвратно удалены') === false) return;

    var form = event.target;

	var request = new XMLHttpRequest();
	request.open(form.method, form.action, true);

	request.onload = function() {
        if (this.status !== 200) {
            Alert.error('Ошибка');
        } else {
            var response, modal;

            response = JSON.parse(this.response);

            if (response.status === 'error') {
                modal = new xModal(response.error, response.msg);
                modal.show();
            } else {
                Alert.success('Удалено!');

                setTimeout(function(){
                    window.location = form.dataset.redirectUrl;
                }, 1900);
            }
        }
	}

	request.send(new FormData(form));
}

window.fillBy = function(inputEl, source, ignore) {
	if (ignore === undefined) ignore = [];

	var request, response;
	var form = document.querySelector('form.create_edit_form');
	var el, infoEl;

	request = new XMLHttpRequest();
	request.open('GET', '/' + source + '/' + inputEl.value + '?json', true);

	request.onload = function() {
		if (this.status != 200) {
			alert('Ошибка');
			return;
		}

		response = JSON.parse(this.response);

		for (name in response) {
			if (ignore.indexOf(name) !== -1) continue;

			el = form.querySelector('[name="' + name + '"]');
            infoEl = form.querySelector('[data-info="' + name + '"]');

            if (el) el.value = response[name];
            if (el && infoEl) infoEl.innerHTML = el.options[el.selectedIndex].text;
		}
	}

	request.send();
}

window.customersCargo = function(inputEl, names) {
	names.forEach(function(name){
		document.querySelectorAll('[name="'+name+'"]').forEach(function(el){
			if (inputEl.checked) {
				el.disabled = true;
				el.required = false;
				el.parentElement.querySelector('label > span').style.display = 'none';
			} else {
				el.disabled = false;
				el.required = true;
				el.parentElement.querySelector('label > span').style.display = '';
			}
		});
	});
}

window.showCommentTextareaInCarEdit = function(inputEl) {
    if (inputEl.value === '') return;

    var div = document.createElement('div');
        div.className = 'form-group';

    var label = document.createElement('label');
        label.setAttribute('for', 'comment');
        label.innerText = 'Информация об оплате:';

    var textarea = document.createElement('textarea');
        textarea.className = 'form-control';
        textarea.name = 'comment';

    div.appendChild(label);
    div.appendChild(textarea);

    inputEl.parentElement.insertAdjacentElement('afterend', div);

    inputEl.onchange = function() {
        if (this.value === '' && document.body.contains(div)) {
            div.remove();
        } else if (this.value !== '' && !document.body.contains(div)) {
            this.parentElement.insertAdjacentElement('afterend', div)
        }
    }
}

window.selectCarAndContract = function (driverSelect) {
    const driverId = $(driverSelect).val();

    $.ajax({
        url: "/drivers/getCar",
        data: {"userId" : driverId},
        success: function (data) {
            if(data.status == 1){
                $("select[name = 'car_id']").val(data.carId);
            }
        },
    });

    $.ajax({
        url: "/drivers/getContract",
        data: {"userId" : driverId},
        success: function (data) {
            if(data.status == 1){
                $("select[name = 'contract_id']").val(data.contractId);
                $("select[name = 'contract_id']").trigger('change');
            }
        },
    });
}

window.toggleSidebar = function () {
    const left_menu = $('#left-sidebar');
    left_menu.animate({'width': 'toggle'});
}

window.showHideSidebar = function () {
    const left_menu = $('#left-menu');
    left_menu.slideToggle(400);
}

window.actionSelect = function ( el )
{
    $( "button[data-select='" + el.value + "'" ).trigger('click');
}


$(document).ready(function(){
    let current_route = window.location.pathname.split('/')[1];
    let current_route_action = window.location.pathname.split('/')[2];

    if (current_route == 'trips' && current_route_action == 'create') {
        if ($("select[name = 'contract_id']")) {
            $("select[name = 'contract_id']").trigger('change');
        }
    }

    $('a.add_row').bind('click', function(){
        let lastIndexRow = $('#file_table').find('tbody tr').length;
        let newRowHtml = "<tr>" +
                            "<td>" +
                                "<select class='file_table_document_type' name='trip_files["+lastIndexRow+"][document_type]' id=''>" +
                                    "<option value=''>Выберите тип документа</option>"+
                                    "<option value='1'>Товарно-транспортная накладная</option>" +
                                    "<option value='2'>Транспортная накладная</option>" +
                                    "<option value='3'>Товарная накладная</option>" +
                                    "<option value='4'>Акт оказания услуг</option>" +
                                    "<option value='5'>Другой</option>" +
                                "</select>" +
                            "</td>"+
                            "<td><input type='text' class='file_table_number' name='trip_files["+lastIndexRow+"][file_number]' value='"+lastIndexRow+"'></td>"+
                            "<td><input type='file' name='trip_files["+lastIndexRow+"][file]'></td>"+
                            "<td><input type='text' name='trip_files["+lastIndexRow+"][file_comment]'></td>"+
                            "<td><a class='btn btn-sm btn-danger remove_row'>Удалить</a></td>"+
                            "</tr>";
        $('#file_table').find('tbody').append( newRowHtml );
    });

    $("input[name='distance'], input[name=distance_price]").change(function(){
        let driver_salary;
        if ($( "input[name='distance_price']" ).val()) {
            driver_salary = $( "input[name='distance']" ).val() * $( "input[name='distance_price']" ).val();
        } else {
            let price_per_km = $("input[name='distance']").data('price-per-km');
            driver_salary = $( "input[name='distance']" ).val() * price_per_km;
        }

        $("input[name='driver_salary']").val(driver_salary);
    })

    $('body').on('click', 'a.remove_row', function(){
       $(this).closest('tr').remove();
    });

    $('body').on('click', 'a#download_docs_trip', function(){
        let trip_id = $(this).attr('data-id');
        window.location.href = '/zip/trip/'+trip_id;
    });

    $('.sub-menu ul').hide();
    $(".sub-menu > a").click(function (e) {
        $(this).parent(".sub-menu").children("ul").slideToggle("100");
        $(this).find(".right").toggleClass("fa-caret-up fa-caret-down");

        e.preventDefault();
    });

    $('#left-menu .li-parent').each(function(i){
        $(this).attr('data-list-id', i);
    });

    if(Cookies.get('data-list-id') != null) {
        var dataListIdsString = Cookies.get('data-list-id');
        var dataListIds = [];
        if (dataListIdsString) {
            dataListIds = dataListIdsString.split(',');
        }

        dataListIds.forEach(function (id) {
            if ($('#left-menu').find('.li-parent[data-list-id="' + id + '"]').find('a[href*=' + current_route.replace('#', '') + ']').length) {
                $('#left-menu').find('.li-parent[data-list-id="' + id + '"]').
                    find('ul a[href*=' + current_route.replace('#', '') + ']').css('font-weight', 'bold');
                $('#left-menu').find('.li-parent[data-list-id="' + id + '"]').css('background-color', '#074e86');
            }
            $('#left-menu').find('.li-parent[data-list-id="' + id + '"]').children("ul").slideToggle("100");
            $('#left-menu').find('.li-parent[data-list-id="' + id + '"]').find(".right").toggleClass("fa-caret-up fa-caret-down");
        });
    }
    $('#left-menu').find('.li-parent').click(function(e) {
        var link = e.target;
        var dataListIdsString = Cookies.get('data-list-id');
        var dataListIds = [];
        var id = $(this).attr('data-list-id');
        if (dataListIdsString) {
            dataListIds = dataListIdsString.split(',');
        }
        if (link.getAttribute('href') == '#') {
            if (dataListIds.indexOf(id) != -1) {
                while (dataListIds.indexOf(id) != -1) {
                    dataListIds.splice(dataListIds.indexOf(id), 1);
                }
            } else {
                dataListIds.push(id);
            }
        } else {
            if (dataListIds.indexOf(id) == -1) {
                dataListIds.push(id);
            }
        }
        Cookies.set('data-list-id', dataListIds.join(','), { expires: 90, path: '/'});
    });

    let getOrganization = (organizationId, source) => {
        $.ajax({
            method: 'GET',
            url: '/' + source + '/' + organizationId + '?json',
            success: function (data) {
                $('input[name=distance]').attr('data-price-per-km', data.price_per_km)
            },
        });
    }

    if ($('body').attr("data-organization")) {
        let organizationId = $('body').attr("data-organization");

        getOrganization(organizationId, 'organizations');
    }

    window.selectOrganization = function (organizationSelect, source) {
        const organizationId = $(organizationSelect).val();

        getOrganization(organizationId, source);
    }
});


let maskPhone = () => {
    let inputPhone = $('input[name=phone]');
    inputPhone.attr('maxlength', 18).attr('placeholder', '+7 (XXX) XXX-XX-XX');

    inputPhone.keydown(function (e) {
        let key = e.which || e.charCode || e.keyCode || 0;
        let phone = $(this);

        if (phone.val().length > 18) {
            return false;
        }

        if (phone.val().length === 3 && (key === 8 || key === 46)) {
            phone.val('+7 ');
            return false;
        } else if (phone.val().charAt(0) !== '+') {
            phone.val('+7 ');
            return false;
        }

        if (key !== 8 && key !== 9) {
            if (phone.val().length === 3) {
                phone.val(phone.val() + '(');
            }
            if (phone.val().length === 7) {
                phone.val(phone.val() + ')');
            }
            if (phone.val().length === 8) {
                phone.val(phone.val() + ' ');
            }
            if (phone.val().length === 12) {
                phone.val(phone.val() + '-');
            }
            if (phone.val().length === 15) {
                phone.val(phone.val() + '-');
            }
        }

        return (key == 8 || key == 9 || key == 46 ||
            (key >= 48 && key <= 57) || (key >= 96 && key <= 105));
    })
        .bind('focus click', function () {
            let phone = $(this);

            if (phone.val().length === 0) {
                phone.val('+7 ');
            } else {
                let val = phone.val();
                phone.val('').val(val);
            }
        })
        .blur(function () {
            let phone = $(this);

            if (phone.val() === '+7 ') {
                phone.val('');
            }
        });
}

$(function() {
    maskPhone();
});
