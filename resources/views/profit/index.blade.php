@extends('layouts.crud')
@section('crud-content')
<h2>Оценка доходности заказа</h2>
<form class="col-md-10 offset-md-2 create_edit_form" method="post" action="{{route('set_temp_data_before_register')}}" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="date">
            Дата:
            <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input type="date" class="form-control" data-name="date" onchange="inputTethering(this, event)" onclick="inputTethering(this, event)" value="2021-07-01" required="required">
            <input type="time" class="form-control" data-name="date" onchange="inputTethering(this, event)" onclick="inputTethering(this, event)" value="17:20" required="required">
        </div>
        <input type="hidden" class="hidden" name="date" value="2021-07-01 17:20" required="required">
    </div>
    <div class="form-group">
        <label for="name">
            Название:
            <span class="text-danger">*</span>
        </label>
        <input class="form-control" required="required" name="name" type="text" value="">
    </div>
    <div class="form-group">
        <label for="distance">
            Расстояние рейса, км:
            <span class="text-danger">*</span>
        </label>
        <input class="form-control" required="required" name="distance" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
    </div>
    <h4 style="padding-top: 26px; min-height: 5px;">Покупка</h4>
    <div class="form-group">

        <label for="loading_volume">
            Объём погрузки за рейс, ед.:
            <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input class="form-control" required="required" name="loading_volume" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
            <select style="width: 100%;" class="form-control " required="required" name="loading_volume_type_id">
                <option value="" style="display:none;"></option>
                <option value="тонна">тонна</option>
                <option value="куб.м">куб.м</option>
                <option value="машина">машина</option>
                <option value="усл.ед">усл.ед</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="loading_price">
            Цена за ед.груза, Р.:
            <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input class="form-control" required="required" name="loading_price" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
            <select style="width: 100%;" class="form-control " required="required" name="loading_price_type_id">
                <option value="" style="display:none;"></option>
                <option value="тонна">тонна</option>
                <option value="куб.м">куб.м</option>
                <option value="машина">машина</option>
                <option value="усл.ед">усл.ед</option>
            </select>
        </div>
    </div>
    <h4 style="padding-top: 26px; min-height: 5px;">Продажа</h4>
    <div class="form-group">
        <label for="unloading_volume">
            Объём отгрузки заказчику за рейс, ед.:
            <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input class="form-control" required="required" name="unloading_volume" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
            <select style="width: 100%;" class="form-control " required="required" name="unloading_volume_type_id">
                <option value="" style="display:none;"></option>
                <option value="тонна">тонна</option>
                <option value="куб.м">куб.м</option>
                <option value="машина">машина</option>
                <option value="усл.ед">усл.ед</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="unloading_price">
            Цена за ед.груза, Р:
            <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input class="form-control" required="required" name="unloading_price" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
            <select style="width: 100%;" class="form-control " required="required" name="unloading_price_type_id">
                <option value="" style="display:none;"></option>
                <option value="тонна">тонна</option>
                <option value="куб.м">куб.м</option>
                <option value="машина">машина</option>
                <option value="усл.ед">усл.ед</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="conversion_factor">
            Коэффициент пересчёта тонна/куб.м (сколько тонн весит 1 куб.м груза):
            <span class="text-danger">*</span>
        </label>
        <input class="form-control" required="required" name="conversion_factor" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
    </div>
    <div class="form-group">
        <label for="additional_overhead">
            Дополнительные накладные расходы, руб.:
            <span class="text-danger">*</span>
        </label>
        <input class="form-control" required="required" name="additional_overhead" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
    </div>
    <h4 style="padding-top: 26px; min-height: 5px;">Настройки</h4>
    <div class="form-group">
        <label for="trip_direction_id">
            Понятие "РЕЙС":
            <span class="text-danger">*</span>
        </label>
        <select style="width: 100%;" class="form-control " required="required" name="trip_direction_id">
            <option value="" style="display:none;"></option>
            <option value="1" selected="selected">от пункта погрузки до пункта разгрузки</option>
            <option value="2">от пункта погрузки до пункта погрузки</option>
        </select>
    </div>
    <div class="form-group">
        <label for="price_of_fuel">
            Цена топлива, руб./л:
            <span class="text-danger">*</span>
        </label>
        <input class="form-control" required="required" name="price_of_fuel" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
    </div>
    <div class="form-group">
        <label for="average_fuel_consumption">
            Средний расход топлива ТС, л/100 км:
            <span class="text-danger">*</span>
        </label>
        <input class="form-control" required="required" name="average_fuel_consumption" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
    </div>
    <div class="form-group">
        <label for="average_fuel_consumption">
            Средний расход топлива ТС, л/100 км:
            <span class="text-danger">*</span>
        </label>
        <input class="form-control" required="required" name="average_fuel_consumption" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
    </div>
    <div class="form-group">
        <label for="fixed_overhead">
            Постоянные накладные расходы на рейс, руб.:
            <span class="text-danger">*</span>
        </label>
        <input class="form-control" required="required" name="fixed_overhead" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
    </div>
    <div class="form-group">
        <label for="driver_salary">
            Заработная плата водителя ТС, руб.:
            <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <input class="form-control" required="required" name="driver_salary" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);">
            <select style="width: 100%;" class="form-control " required="required" name="driver_salary_type_id">
                <option value="" style="display:none;"></option>
                <option value="1">за рейс</option>
                <option value="2">за 1 км</option>
                <option value="3">за ед. груза при разгрузке</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="driver_salary_direction_id">
            Расчёт заработной платы водителя ТС:
            <span class="text-danger">*</span>
        </label>
        <select style="width: 100%;" class="form-control " required="required" name="driver_salary_direction_id">
            <option value="" style="display:none;"></option>
            <option value="1" selected="selected">от пункта погрузки до пункта разгрузки</option>
            <option value="2">от пункта погрузки до пункта погрузки</option>
        </select>
    </div>
    <div class="form-group">
        <label for="with_taxes">
            Учесть в з/п водителя социальные налоги и НДФЛ:
            <span class="text-danger">*</span>
        </label>
        <div style="margin-left: 1rem;" class="custom-control custom-radio custom-control-inline">
            <input type="radio" name="with_taxes" id="with_taxes1" class="custom-control-input" value="1" required="required">
            <label class="custom-control-label" for="with_taxes1">
                Да
            </label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="with_taxes2" name="with_taxes" class="custom-control-input" value="0" required="required" checked="checked">
            <label class="custom-control-label" for="with_taxes2">
                Нет
            </label>
        </div>
    </div>
    <h4 style="padding-top: 26px; min-height: 5px;">Учёт НДС</h4>
    <div class="form-group">
        <label for="vat_in_income">
            В доходах:
            <span class="text-danger">*</span>
        </label>
        <div style="margin-left: 1rem;" class="custom-control custom-radio custom-control-inline">
            <input type="radio" name="vat_in_income" id="vat_in_income1" class="custom-control-input" value="1" required="required" checked="checked">
            <label class="custom-control-label" for="vat_in_income1">
                Да
            </label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="vat_in_income2" name="vat_in_income" class="custom-control-input" value="0" required="required">
            <label class="custom-control-label" for="vat_in_income2">
                Нет
            </label>
        </div>
    </div>
    <div class="form-group">
        <label for="vat_in_fuel_expenses">
            В расходах на топливо:
            <span class="text-danger">*</span>
        </label>
        <div style="margin-left: 1rem;" class="custom-control custom-radio custom-control-inline">
            <input type="radio" name="vat_in_fuel_expenses" id="vat_in_fuel_expenses1" class="custom-control-input" value="1" required="required" checked="checked">
            <label class="custom-control-label" for="vat_in_fuel_expenses1">
                Да
            </label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="vat_in_fuel_expenses2" name="vat_in_fuel_expenses" class="custom-control-input" value="0" required="required">
            <label class="custom-control-label" for="vat_in_fuel_expenses2">
                Нет
            </label>
        </div>
    </div>
    <div class="form-group">
        <label for="vat_in_cargo_expenses">
            В расходах на груз:
            <span class="text-danger">*</span>
        </label>
        <div style="margin-left: 1rem;" class="custom-control custom-radio custom-control-inline">
            <input type="radio" name="vat_in_cargo_expenses" id="vat_in_cargo_expenses1" class="custom-control-input" value="1" required="required" checked="checked">
            <label class="custom-control-label" for="vat_in_cargo_expenses1">
                Да
            </label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="vat_in_cargo_expenses2" name="vat_in_cargo_expenses" class="custom-control-input" value="0" required="required">
            <label class="custom-control-label" for="vat_in_cargo_expenses2">
                Нет
            </label>
        </div>
    </div>
    <div class="form-group">
        <label for="vat_in_fixed_overhead">
            В постоянных накладных расходах:
            <span class="text-danger">*</span>
        </label>
        <div style="margin-left: 1rem;" class="custom-control custom-radio custom-control-inline">
            <input type="radio" name="vat_in_fixed_overhead" id="vat_in_fixed_overhead1" class="custom-control-input" value="1" required="required">
            <label class="custom-control-label" for="vat_in_fixed_overhead1">
                Да
            </label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="vat_in_fixed_overhead2" name="vat_in_fixed_overhead" class="custom-control-input" value="0" required="required" checked="checked">
            <label class="custom-control-label" for="vat_in_fixed_overhead2">
                Нет
            </label>
        </div>
    </div>
    <div class="form-group">
        <label for="vat_in_additional_overhead">
            В дополнительных накладных расходах:
            <span class="text-danger">*</span>
        </label>
        <div style="margin-left: 1rem;" class="custom-control custom-radio custom-control-inline">
            <input type="radio" name="vat_in_additional_overhead" id="vat_in_additional_overhead1" class="custom-control-input" value="1" required="required">
            <label class="custom-control-label" for="vat_in_additional_overhead1">
                Да
            </label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="vat_in_additional_overhead2" name="vat_in_additional_overhead" class="custom-control-input" value="0" required="required" checked="checked">
            <label class="custom-control-label" for="vat_in_additional_overhead2">
                Нет
            </label>
        </div>
    </div>
    <div class="form-group">
        <label for="comment">
            Примечания:
        </label>
        <textarea cols="30" rows="3" class="form-control" name="comment"></textarea>
    </div>
    <div class="w-100 mt-3 mb-4 create_edit_form_buttons">
        <button type="submit" class="btn btn-warning mr-1 without_profitability">
            Создать заказ на основе расчета
        </button>
    </div>
</form>
<script src="{{mix('js/profitability.js')}}"></script>
@endsection
