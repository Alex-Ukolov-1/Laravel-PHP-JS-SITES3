@extends('crud.index')

@section('buttons')
@endsection

@section('title', 'Оплата автомобилей')
@section('h1', 'Оплата автомобилей')

@section('content')

@section('table')

<div class="col-sm-6 col-sm-offset-3">
    <div style="font-size: 16px;">
	    <span>Как только средства поступят на наш счёт, оплаченные автомобили будут активированы</span><br/>
	    <a href="{{ \route('cars.index') }}">Перейти к списку автомобилей</a>
	  </div>
</div>

@show
@endsection
