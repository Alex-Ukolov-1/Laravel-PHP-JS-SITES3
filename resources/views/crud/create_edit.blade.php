@extends('layouts.crud')

@inject('relation', 'App\View\RenderRelation')

@section('crud-content')
    <div class="row">
        <div class="col-sm-8 offset-sm-2">
            <h1>{{ $title }}</h1>

            <div>

                @if(session()->get('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin-bottom:0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br/>
                @endif

                @include('crud._form')

                @if (isset($drivers_history_records) && $drivers_history_records->isNotEmpty())
                    <div class="driversHistoryTable d-flex flex-column">
                        @foreach($drivers_history_records as $record)
                            <span>Машина <b>{{$record->car ? $record->car->number : ''}}</b> присвоена водителю {{$record->created_at}}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (!empty($append_js))
        @foreach ($append_js as $js)
            <script src="{{ mix('/js/' . $js . '.js') }}"></script>
        @endforeach
    @endif

@endsection
