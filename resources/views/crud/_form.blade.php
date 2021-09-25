@php
    if ($type === 'create') $action = route($route . '.store');
    if ($type === 'edit') $action = route($route . '.update', $item->id);
@endphp

<form class="create_edit_form" method="post" action="{{ $action }}" enctype="multipart/form-data">
    @if ($type === 'edit')
        @method('PATCH')
    @endif

    @csrf

    @if(!\Illuminate\Support\Facades\Auth::guard('driver')->check())
        @include('partials.buttons')
    @endif

    @foreach($fields as $key => $field)

        @if ($field['type'] === 'subtitle')
            <h4 style="padding-top: 26px; min-height: 5px;">{{ $field['title'] }}</h4>
            @continue
        @endif

        @if ($field['view'] !== 'end_group')
            <div class="form-group">
        @endif

        @if (!empty($field['title']) && $field['type'] !== 'boolean:checkbox')
            <label for="{{ $field['name'] }}">
                {{ $field['title'] }}:
                @if ($field['required'] === true) <span class="text-danger">*</span> @endif
            </label>
        @endif

        @if ($field['view'] === 'start_group')
            <div class="input-group">
        @endif

        @if ($field['type'] === 'relation')
            {!! $relation->render($field, $type) !!}
        @else
            <x-input :field="$field" :item="$item"/>
        @endif

        @if ($field['view'] === 'end_group')
            </div>
        @endif

        @if ($field['view'] !== 'start_group')
            </div>
        @endif
    @endforeach

    @include('partials.buttons')
</form>
