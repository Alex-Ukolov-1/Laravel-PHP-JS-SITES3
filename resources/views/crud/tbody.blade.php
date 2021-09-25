@php $totals = []; @endphp

@foreach($items as $item)
<tr>
    <td>
        <input type="checkbox" name="ids[]" value="{{$item->id}}" form="datatable-{{$route}}_form">
    </td>
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

      <td data-name="{{$field['name']}}">
        @if ($field['type'] === 'textarea')
          <textarea class="form-control" cols="30" rows="2" readonly="readonly">{{ $data }}</textarea>
        @elseif ($field['type'] === 'boolean')
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
      </td>
    @endforeach

    @foreach ($row_buttons as $row_button_name)
      <td>
        @include('components.row_buttons.'.$row_button_name)
      </td>
    @endforeach
</tr>
@endforeach

@if (!empty($totals))
<tr class="table-primary">
  <td></td>
  @foreach($fields as $field)
    <td style="text-shadow: 0 0 0 #000;">{{ isset($totals[$field['name']]) ? number_format($totals[$field['name']], 2, '.', '') : '' }}</td>
  @endforeach
</tr>
@endif
