<div id="export_trips-{{$route}}" class="export_trips">
	<div class="my-3 buttons-block">
	    <button id="trips-export_btn" data-select="1">Выгрузить</button>
	    <button id="trips-export_btn_zip" data-select="2">Cкачать документы к рейсам</button>
	    <button id="trips-email_export_xls" data-select="3" data-toggle="modal" data-target="#trips-email_modal">Отправить по почте реестр рейсов</button>
	    <button id="trips-email_export_all" data-select="4" data-toggle="modal" data-target="#trips-email_modal">Отправлять архив накладных вместе с реестром рейсов</button>
	</div>
	<select class="form-control my-3 buttons-block-mobi" onchange="actionSelect(this)">
	    <option value="" selected disabled style="display: block;">Действия</option>
	    <option value="2">Выгрузить</option>
	    <option value="3">Cкачать документы к рейсам</option>
	    <option value="4">Отправить по почте реестр рейсов</option>
	    <option value="5">Отправлять архив накладных вместе с реестром рейсов</option>
	</select>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		var dataTable = window.dt.{{$route}};

		var request = new XMLHttpRequest();

		function makeRequest(url) {
			disableButtons();
			Alert.wait('Идёт выгрузка...');

			request.open('POST', url, true);

			request.setRequestHeader('Content-Type', "application/json; charset=UTF-8");
			request.setRequestHeader('X-CSRF-TOKEN', dataTable.token);

			request.responseType = 'blob';

			request.send(JSON.stringify(dataTable.requestData));
		}

		function handleResponse() {
			Alert.hideWait();
			enableButtons();

			if (this.response.type === 'application/json') {
				var reader = new FileReader();

				reader.addEventListener('loadend', (event) => {
				  var response = JSON.parse(event.srcElement.result);

				  if (response.error !== undefined) {
				  	Alert.error('Ошибка. ' + response.error);
				  }

				  if (response.success !== undefined) {
				  	Alert.success(response.success);
				  }
				});

				reader.readAsText(this.response);
			} else {
				var link = document.createElement('a');
				var url = window.URL.createObjectURL(this.response);

				var headerWithFileName = this.getResponseHeader('Content-Disposition');

				var utf8FileName = headerWithFileName.match(/filename\*=utf-8''(.*)/);
				var defaultFileName = headerWithFileName.match(/filename="(.*)"/);

				if (Array.isArray(utf8FileName) && utf8FileName[1] !== undefined) {
					var fileName = decodeURIComponent(utf8FileName[1]);
				} else {
					var fileName = decodeURIComponent(defaultFileName[1]);
				}

				link.href = url;
				link.download = fileName;

				document.body.appendChild(link);
				link.click();

				link.remove();
				window.URL.revokeObjectURL(url);
			}
		}

		request.addEventListener('load', handleResponse);

		var url;
		var xlsExportButton = document.getElementById('trips-export_btn');
		var zipExportButton = document.getElementById('trips-export_btn_zip');
		var emailXlsExportButton = document.getElementById('trips-email_export_xls');
		var emailAllExportButton = document.getElementById('trips-email_export_all');
		var sendEmailButton = document.getElementById('trips-send_email');
		var hrefExportInput = document.getElementById('trips-export_href');;
		var emailExportInput = document.getElementById('trips-export_email');
		var modalCloseButton = document.getElementById('trips-close-btn');

		var buttons = [xlsExportButton, zipExportButton, emailXlsExportButton, emailAllExportButton, sendEmailButton];

		function disableButtons() {
			buttons.forEach(function(button){ button.disabled = true; });
		}

		function enableButtons() {
			buttons.forEach(function(button){ button.disabled = false; });
		}

		xlsExportButton.addEventListener('click', function(){
			makeRequest('/report/trip/xls');
		});

		zipExportButton.addEventListener('click', function(){
			makeRequest('/report/trip/zip_all');
		});

		emailXlsExportButton.addEventListener('click', function(){
			hrefExportInput.value = '/report/trip/email_export_xls';
		});

		emailAllExportButton.addEventListener('click', function(){
			hrefExportInput.value = '/report/trip/email_export_all';
		});

		sendEmailButton.addEventListener('click', function(){
			url = hrefExportInput.value + "?email=" + emailExportInput.value;

			makeRequest(url);

			modalCloseButton.click();
		});
	});
</script>

<!-- Modal -->
<div class="modal fade" id="trips-email_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Email для отправки</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="form-group">
              <label for="loading_cargo_amount">
              Email:  <span class="text-danger">*</span>
              </label>
              <input class="form-control" required="required" name="email" id="trips-export_email" type="email">
              <input type="hidden" id="trips-export_href" value="">
          </div>
      </div>
      <div class="modal-footer">
	        <button type="button" class="btn btn-primary" id="trips-send_email">Отправить</button>
			<button type="button" id='trips-close-btn' class="btn btn-secondary" data-dismiss="modal">Отмена</button>
      </div>
    </div>
  </div>
</div>