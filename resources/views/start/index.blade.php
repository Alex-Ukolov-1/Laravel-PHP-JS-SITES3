@extends('layouts.crud')
@section('crud-content')
    <div class="row">
        <div class="col-sm-12">
            <h1>{{ $title }}</h1>

            @if (auth('organization')->check() || auth('admin')->check())
                <div class="row mb-3">
                    <div class="col-md-4">
                        <a href="/cars/create" class="btn btn-primary btn-lg btn-block">Добавить ТС</a>
                    </div>
                    <div class="col-md-4">
                        <a href="/drivers/create" class="btn btn-primary btn-lg btn-block">Добавить водителя</a>
                    </div>
                    <div class="col-md-4">
                        <a href="/contracts/create" class="btn btn-primary btn-lg btn-block">Добавить заказ</a>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <a href="/trips/create" class="btn btn-primary btn-lg btn-block">Добавить рейс</a>
                    </div>
                    <div class="col-md-4">
                        <a href="/incomes/create" class="btn btn-primary btn-lg btn-block">Добавить доход</a>
                    </div>
                    <div class="col-md-4">
                        <a href="/expenses/create" class="btn btn-primary btn-lg btn-block">Добавить расход</a>
                    </div>
                </div>
            @endif

            @if (auth('driver')->check())
                <div class="row">
                    <div class="col-md-4">
                        <a href="/trips/create" class="btn btn-primary btn-lg btn-block">Добавить рейс</a>
                    </div>
                    <div class="col-md-4">
                        <a href="/incomes/create" class="btn btn-primary btn-lg btn-block">Добавить доход</a>
                    </div>
                    <div class="col-md-4">
                        <a href="/expenses/create" class="btn btn-primary btn-lg btn-block">Добавить расход</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
