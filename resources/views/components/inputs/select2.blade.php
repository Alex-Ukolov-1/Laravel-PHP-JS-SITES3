<select style="width: 100%;" class="form-control {{ $name }}-select" {{ $attributes->merge($xAttributes) }}>
			<option value="" @if ($required) style="display:none;" @endif></option>

		@foreach ($list as $value => $text)
			<option value="{{ $value }}" @if ($selected_value === (string)$value) selected="selected" @endif>{{ $text }}</option>
		@endforeach
</select>

<script>
	{{-- Инициализация select2.js --}}

	$('.{{ $name }}-select').select2({
	    ajax: {
	        url: '/{{ $source_route }}/list',
	        dataType: 'json',
	        delay: 250,
	        processResults: function(data) {
	            return {
	                results: data
	            }
	        }
	    },
        "language": {
            "noResults": function(){
                return '{{$title}} не найден, нажмите <button onclick="Modal.showModal(\'.{{ $name }}-modal\', function() { $(\'.{{ $name }}-select\').select2(\'close\'); }, function() { $(\'.{{ $name }}-select\').select2(\'open\'); })" type="button" class="btn btn-sm btn-success btn-cargo-add" title="Добавить {{$title}}">Добавить</button>';
            }
        },
	    escapeMarkup: function(markup) { return markup; },
	});


        $('.{{ $name }}-select').on('select2:open', function (e) {
            if (!$('.btn-cargo-add').length) {
                $('.select2-search__field').css('width', 'calc(100% - 35px)');
                $('.select2-search--dropdown').append('' +
                    '<button onclick="Modal.showModal(\'.{{ $name }}-modal\', function() { $(\'.{{ $name }}-modal\').find(\'input[name=name]\').val($(\'.{{ $name }}-select\').data(\'select2\').dropdown.$search.val()); $(\'.{{ $name }}-select\').select2(\'close\'); }, function() { })" type="button" class="btn btn-sm btn-success btn-cargo-add" title="Добавить {{$title}}"><i class="fa fa-plus"></i></button>');
            }
        });

</script>

@push('modals')
	<x-select2-modal-form :controller="$controller" :name="$name" :sourceroute="$source_route" />
@endpush
