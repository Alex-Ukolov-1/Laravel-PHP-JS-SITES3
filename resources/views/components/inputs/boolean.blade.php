<label style="margin-left: 1rem;" class="custom-control custom-radio custom-control-inline">
  <input
  	type="radio"
    name="{{ $name }}"
  	class="custom-control-input"
  	value="1"
    @if ($required === true) required="required" @endif
    @if ($selected_value === '1') checked="checked" @endif
  />
  <span class="custom-control-label">  
    @if(isset($boolean_turn_on))
        {{$boolean_turn_on}}
    @else
      Да
    @endif
  </span>
</label>
<label class="custom-control custom-radio custom-control-inline">
  <input
  	type="radio"
  	name="{{ $name }}"
  	class="custom-control-input"
  	value="0"
    @if ($required === true) required="required" @endif
  	@if ($selected_value === '0') checked="checked" @endif
  />
  <span class="custom-control-label">
    @if(isset($boolean_turn_off))
        {{$boolean_turn_off}}
    @else
        Нет
    @endif
  </span>
</label>
