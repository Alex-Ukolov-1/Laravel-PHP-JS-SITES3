<div id="datatable-{{$route}}"
     data-route="{{$route}}"
     data-sort="{{ $sort->field . ',' . $sort->order }}">

    <h1>{{ $title }}</h1>

        @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
        </div>
        @endif
    <form method="POST" id="datatable-{{$route}}_form">
      @csrf
    </form>
    <div class="d-flex" style="zoom:70%">

        <div class="d-flex align-items-center pagination">

            @foreach($top_buttons as $top_button_name)
              @include('components.top_buttons.'.$top_button_name)
            @endforeach

            <div class="d-flex ">
                <span class="d-inline-block" style="width: 60px; padding-top: 10px">На странице: </span>
                <select class="per_page-selector form-control mr-3" style="width: 60px;" name="per_page">
                    <option value="10">10</option>
                    <option value="25" selected="selected">25</option>
                    <option value="50">50</option>
                    <option value="75">75</option>
                    <option value="100">100</option>
                </select>
            </div>
            <nav class="d-flex align-items-center ml-auto datatable-pagination"></nav>
        </div>

        @foreach($plugins as $plugin)
          @include('components.dt_plugins.'.$plugin)
        @endforeach

    </div>

    <table class="table table-striped table-hover" id="table" style="zoom:50%">
        <thead>
        <tr>
            <th class="datatable__title-cell"></th>

            @foreach($fields as $field)
           
                <th style="width:2%" class="datatable__title-cell @php if($field['type'] === 'datetime') echo 'date' @endphp">
                     <div id="controls" style="display:block;">                     
                     <input type="checkbox" id="<?php echo $field['name']; ?>"  data-column-class="<?php echo $field['name']; ?>" checked>
                     </div>
                     
                    
                    <span class="datatable__title-cell__text" >{{ isset($field['table_title']) ? $field['table_title'] : $field['title'] }}</span>

                    @php if ($field['type'] === 'multiselect-labels') $field['type'] = 'select'; @endphp
                    @php if ($field['type'] === 'select2') $field['type'] = 'select'; @endphp
                    @php if ($field['type'] === 'textarea') $field['type'] = 'text'; @endphp
                    @php if ($field['type'] === 'float') $field['type'] = 'text'; @endphp
                    @php if ($field['type'] === 'email') $field['type'] = 'text'; @endphp
                    @php if ($field['type'] === 'phone') $field['type'] = 'text'; @endphp
                    @php if ($field['type'] === 'datetime') $field['type'] = 'date'; @endphp

                    @if ($field['type'] === 'select')
                        <select
                                class="form-control form-control-sm"
                                name="{{ $field['name'] }}"
                        >
                            @php
                                if (is_string($field['source'])) {
                                    $source = new $field['source'];
                                    if ($field['scope']) $source = $source->{$field['scope']}();
                                    $values = $source->fullList();
                                } else if (is_array($field['source'])) {
                                    $values = $field['source'];
                                }
                            @endphp
                            <option value=""></option>
                            @foreach ($values as $id => $value)
                                <option value="{{ $id }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    @elseif ($field['type'] === 'boolean')
                        <select class="form-control form-control-sm" name="{{ $field['name'] }}">
                            <option value=""></option>
                            <option value="1">
                                @if(isset($field['boolean_turn_on']))
                                    {{$field['boolean_turn_on']}}
                                @else
                                    Да
                                @endif
                            </option>
                            <option value="0">
                                @if(isset($field['boolean_turn_off']))
                                    {{$field['boolean_turn_off']}}
                                @else
                                    Нет
                                @endif
                            </option>
                        </select>
                    @elseif ($field['type'] === 'date')
                        <input style="display: inline-block; width: 100px;"
                               type="{{ $field['type'] }}"
                               class="form-control form-control-sm date-range"
                               name="{{ $field['name'] }}"
                        />
                    @else
                        <input
                                type="{{ $field['type'] }}"
                                class="form-control form-control-sm"
                                name="{{ $field['name'] }}"
                                @if ($field['strict_search'] === '*') data-strict="*"
                                @elseif ($field['strict_search'] === true) data-strict="true"
                                @elseif ($field['strict_search'] === false || $field['type'] === 'text' || $field['type'] === 'number') data-strict="false"
                                @endif
                        />
                    @endif
                    <svg  data-name="{{ $field['name'] }}"
                         @if (isset($sort) && $sort->field == $field['name'])
                            data-order="{{ $sort->order }}"
                         @else
                            data-order="ASC"
                         @endif
                         class="bi bi-arrow-up-down datatable__sort-column-icon" width="1em" height="1em"
                         viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path class="sort-arrow sort-arrow-asc" @if (isset($sort) && $sort->field == $field['name'] && $sort->order == 'ASC') fill="#ff0000" @endif fill-rule="evenodd" d="M13 5.5a.5.5 0 01.5.5v9a.5.5 0 01-1 0V6a.5.5 0 01.5-.5z"
                                      clip-rule="evenodd"></path>
                        <path class="sort-arrow sort-arrow-desc" @if (isset($sort) && $sort->field == $field['name'] && $sort->order == 'DESC') fill="#ff0000" @endif  fill-rule="evenodd"
                              d="M 12.646 4.646 z m -9 7 a 0.5 0.5 0 0 1 0.708 0 L 7 14.293 l 2.646 -2.647 a 0.5 0.5 0 0 1 0.708 0.708 l -3 3 a 0.5 0.5 0 0 1 -0.708 0 l -3 -3 a 0.5 0.5 0 0 1 0 -0.708 z"
                              clip-rule="evenodd"></path>
                        <path class="sort-arrow sort-arrow-asc" @if (isset($sort) && $sort->field == $field['name'] && $sort->order == 'ASC') fill="#ff0000" @endif fill-rule="evenodd"
                              d="M 12.646 4.646 a 0.5 0.5 0 0 1 0.708 0 l 3 3 a 0.5 0.5 0 0 1 -0.708 0.708 L 13 5.707 l -2.646 2.647 a 0.5 0.5 0 0 1 -0.708 -0.708 z a 0.5 0.5 0 0 1 0.708 0.708 z"
                              clip-rule="evenodd"></path>
                        <path class="sort-arrow sort-arrow-desc" @if (isset($sort) && $sort->field == $field['name'] && $sort->order == 'DESC') fill="#ff0000" @endif  fill-rule="evenodd" d="M7 4.5a.5.5 0 01.5.5v9a.5.5 0 01-1 0V5a.5.5 0 01.5-.5z"
                              clip-rule="evenodd"></path>
                    </svg>
                </th>
            @endforeach
            <th colspan="2" ></th>
        </tr>
        </thead>
        <tbody  id="<?php echo $field['name']; ?>">
        </tbody>
        </div>
    </table>

</div>
<div class="datatable-stats  align-items-center justify-content-between  mt-4" style="zoom:70%">
    <div class="PageStatsBlock" style="display:inline;">
        Показано от <span class="lowest">1</span> до <span class="highest">1</span> из <span class="total">1</span> записей
    </div>
    <nav class=" align-items-center justify-content-end datatable-pagination"></nav>

<script>
  document.querySelectorAll('#datatable-{{$route}} th > select').forEach(function(select){
    new SelectWithInput(select);
  });

  document.querySelectorAll('#datatable-{{$route}} th > .date-range').forEach(function(input){
    new DateRange(input);
  });

  if (window.dt === undefined) window.dt = {};

  window.dt.{{$route}} = new DataTable('#datatable-{{$route}}');
</script>
<script>
(function () {
var $window = $(window), $body = $("table");
var ie = document.documentMode;
  
function updateSizes() {
var width = $window.width(), scale = Math.min(width / 45% , 1);

var style = $body[0].style;
    
style.msZoom = ie === 8 || ie === 9 ? scale : 1;
style.zoom = ie === 10 || ie === 11 ? 1 : scale;
style.mozTransform = "scale(" + scale + ")";
style.oTransform = "scale(" + scale + ")";
style.transform = "scale(" + scale + ")";
}

$window.resize(updateSizes);
updateSizes();
}());
</script>
<!-- по поводу скрытия столбцов у определенных checkbox данный способ работает у меня на других проектах,тут я без понятия что не так  -->
<!--<script src="js/jquery-3.6.0.min.js"></script>-->
<!--<script src="js/pagination.js"></script>-->
<!--<script>-->   
<!--$(function(){-->
<!--$(':checkbox').on('change', function(){-->
        <!--$('th, td', 'tr').filter(':nth-child(' + $(this).data('name') + ')').toggle();-->
    <!--});-->
<!--});-->
<!--</script>-->
