<div class="custom-control custom-checkbox">
  <input type="checkbox" class="custom-control-input" id="{{ $name }}" value="1" {{ $attributes->merge($xAttributes) }} />
  <label class="custom-control-label" for="{{ $name }}">{{ mb_strtolower($title) }}</label>
</div>

@if (isset($xAttributes['onchange']))
<script>
document.addEventListener('DOMContentLoaded', function(){
  document.getElementById('{{$name}}').onchange();
});
</script>
@endif