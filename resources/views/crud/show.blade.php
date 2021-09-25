@extends('layouts.crud')

@section('crud-content')
<div class="row">
	<div class="col-sm-8 offset-sm-2" style="overflow-x: auto;">
    <div class="w-100 mt-3 mb-4 create_edit_form_buttons">
      <a class="btn btn-primary mr-1" href="{{ route($route . '.edit', $item->id) }}">Редактировать</a>
      <form  onsubmit="deleteButtonOnShowPage(event);" action="{{ route($route . '.destroy', $item->id) }}" data-redirect-url="{{ route($route . '.index') }}" method="post">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger mr-1" type="submit">Удалить</button>
      </form>
      <a class="btn btn-secondary mr-1 float-right" href="{{ \request()->server('HTTP_REFERER') ?? route($route . '.index') }}">Отмена</a>
    </div>
    <h1>{{ $title }}</h1>
	  <div>
      @foreach($fields as $field)
      @php
      if (strpos($field['data'], '->') !== false) {
        $p = explode('->', $field['data']);
        $data = $item;
        while($s = array_shift($p)) {
          if ($data === null) break;
          if (gettype($data) !== 'object') dd($s);
          $data = $data->{$s};
        }
      } else {
          $data = $item->{$field['data']};
      }

      if ($field['with_total'] === true) {
        if (!isset($totals[$field['name']])) $totals[$field['name']] = 0;
        $totals[$field['name']] += (float)$data;
      }
      @endphp

      <div class="form-group">
      	<span style="font-weight: bold;">{{ $field['title'] }}: </span>

        @if ($field['type'] === 'textarea')
          <textarea class="form-control" cols="30" rows="2" readonly="readonly">{{ $data }}</textarea>
        @else
        <span>
          @if ($field['type'] === 'boolean')
            @if ((string)$data === '1')
              @if(isset($field['boolean_turn_on']))
                {{ $field['boolean_turn_on'] }}
              @else
                Да
              @endif
            @endif
            @if ((string)$data === '0')
              @if(isset($field['boolean_turn_off']))
                {{ $field['boolean_turn_off'] }}
              @else
                Нет
              @endif
            @endif
          @elseif ($field['type'] === 'datetime' || $field['type'] === 'date')
            @if ($data)
              {{ (new \Carbon\Carbon($data))->format('d.m.Y') }}
            @endif
          @elseif ($field['type'] === 'float')
            {{ $field['value_prepend'] ?? '' }}{{ number_format($data, 2, '.', ' ') }}{{ $field['value_append'] ?? '' }}
          @else
            {{ $field['value_prepend'] ?? '' }}{{ $data }}{{ $field['value_append'] ?? '' }}
          @endif
        </span>
        @endif

      </div>
      @endforeach
	  </div>
	</div>
</div>
@endsection

