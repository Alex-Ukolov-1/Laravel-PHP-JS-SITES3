@extends('layouts.crud')

@inject('relation', 'App\View\RenderRelation')

@section('crud-content')
    <div class="row">
        <div class="col-sm-8 offset-sm-2">
            <h1>{{ $title }}</h1>

            @if (!$contract_exists)
                <div class="alert alert-danger">
                   Невозможно создать рейс, поскольку вам не назначен заказ
                </div>
            @endif

            @if (!$car_exists)
                <div class="alert alert-danger">
                   Невозможно создать рейс, поскольку вам не назначен автомобиль
                </div>
            @endif

            <button type="button" class="btn btn-secondary mr-1"
                    onclick="location='{{ \request()->server('HTTP_REFERER') ?? route($route . '.index') }}'">Назад
            </button>
        </div>
    </div>

@endsection
