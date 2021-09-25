<div class="input-group">
	<input
		type="date"
		class="form-control"
		data-name="{{ $name }}"
		onchange="inputTethering(this, event)"
		onclick="inputTethering(this, event)"
		value="{{ $date }}"
		@if ($required === true) required="required" @endif
	/>
	<input
		type="time"
		class="form-control"
		data-name="{{ $name }}"
		onchange="inputTethering(this, event)"
		onclick="inputTethering(this, event)"
		value="{{ $time }}"
		@if ($required === true) required="required" @endif
	/>
</div>
<input
	type="hidden"
	class="hidden"
	name="{{ $name }}"
	value="{{ $value }}"
	@if ($required === true) required="required" @endif
/>