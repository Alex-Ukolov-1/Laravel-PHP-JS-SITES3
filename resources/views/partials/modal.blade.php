<div class="modal fade {{ $name }}-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{ $title }}. Создание</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                {!! $form !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button onclick="Form.send('.{{ $name }}-modal form', '/{{ $source_route }}', function(data) { $('.{{ $name }}-modal').modal('hide'); var newOption = new Option(data.name, data.id, false, true); $('.{{ $name }}-select').append(newOption).trigger('change'); })" type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>