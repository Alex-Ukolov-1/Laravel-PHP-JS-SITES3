<select style="width: 100%;" class="form-control @if($class){{$class}}@endif" {{ $attributes->merge($xAttributes) }}>
		<option value="" @if ($required) style="display:none;" @endif></option>

	@foreach ($list as $value => $text)
		<option value="{{ $value }}" @if ($selected_value === (string)$value) selected="selected" @endif>{{ $text }}</option>
	@endforeach
</select>
