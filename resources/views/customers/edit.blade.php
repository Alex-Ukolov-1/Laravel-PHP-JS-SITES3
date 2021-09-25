@extends('layouts.crud')

@inject('relation', 'App\View\RenderRelation')


@section('crud-content')
    <div class="row">
        <div class="col-sm-8 offset-sm-2">
            <h1>{{$title}}</h1>

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

                <form class="create_edit_form" method="post" action="{{route($route . '.update', $item->id)}}">
                    @csrf
                    @method('PATCH')

                    @include('partials.buttons')

                    <div class="form-group">
                        <label for="name">Название: <span class="text-danger">*</span></label>
                        <input class="form-control" required="required" name="name[]" type="text"
                               value="{{$item->name}}"/>
                    </div>

                    <div class="form-group">
                        <label for="email">E-Mail: </label>
                        <input class="form-control" name="email[]" type="text" value="{{$item->email}}"/>
                    </div>


                    <div class="form-group">
                        <label for="phone">Телефон: </label>
                        <input class="form-control" name="phone[]" type="text" value="{{$item->phone}}"/>
                    </div>

                    <div class="form-group">
                        <label for="note">Примечание: </label>
                        <textarea cols="30" rows="3" class="form-control" name="note[]">{{$item->note}}</textarea>
                    </div>

                    @if($item->children)
                        @foreach($item->children as $children)
                            <div class="form-group input_fields_wrap">
                                <div class="card input_fields_wrap">
                                    <div class="card-body">
                                        <label for="name">Название: <span class="text-danger">*</span></label>
                                        <input class="form-control" required="required" name="name[]" type="text" value="{{$children->name}}"/><br>

                                        <label for="email">E-Mail: </label>
                                        <input class="form-control" name="email[]" type="text" value="{{$children->email}}"/><br>

                                        <label for="phone">Телефон: </label>
                                        <input class="form-control" name="phone[]" type="text" value="{{$children->phone}}"/><br>

                                        <label for="note">Примечание: </label>
                                        <textarea cols="30" rows="3" class="form-control" name="note[]">{{$children->comment}}</textarea><br>
                                        <button class="btn btn-danger remove_field">Скрыть поля</button><br>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    @endif

                    <div class="form-group input_fields_wrap">

                    </div>

                    <div>
                        <button class="btn btn-success add_field_button">Добавить поле</button>
                    </div>


                    <script>
                        $(document).ready(function () {
                            var max_fields = 10; //maximum input boxes allowed
                            var wrapper = $(".input_fields_wrap"); //Fields wrapper
                            var add_button = $(".add_field_button"); //Add button ID

                            var x = 1; //initlal text box count
                            $(add_button).click(function (e) { //on add input button click
                                e.preventDefault();
                                if (x < max_fields) { //max input box allowed
                                    x++; //text box increment
                                    $(wrapper).append('<div class="form-group input_fields_wrap"><div class="card input_fields_wrap"><div class="card-body"><div>' +
                                        '<label for="name">Название: <span class="text-danger">*</span></label><input class="form-control" required="required" name="name[]" type="text" value=""/><br>' +
                                        '<label for="email">E-Mail:</label><input class="form-control" name="email[]" type="text" value=""/><br>' +
                                        '<label for="phone">Телефон: </label><input class="form-control" name="phone[]" type="text" value=""/><br>' +
                                        '<label for="note">Примечание: </label><textarea cols="30" rows="3" class="form-control note" name="note[]"></textarea><br>' +
                                        '<button class="btn btn-danger remove_field">Скрыть поля</button><br>' +
                                        '</div></div></div>'); //add input box
                                }
                            });

                            $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
                                e.preventDefault();
                                $(this).parent('div').remove();
                                x--;
                            })
                        });
                    </script>

                    <div class="form-group">
                        <label for="counterparty_type_id">Тип контрагента: <span class="text-danger">*</span> </label>
                        <select class="form-control" required="required" name="counterparty_type_id">
                            @if($item->counterparty_type_id === 1)
                                <option value="1">Заказчик</option>
                            @elseif($item->counterparty_type_id === 2)
                                <option value="2">Поставщик</option>
                            @elseif($item->counterparty_type_id === 3)
                                <option value="3">Исполнитель</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="inn">ИНН: </label>
                        <input class="form-control" name="inn" type="text" value="{{$item->inn}}"/>
                    </div>


                    <div class="form-group">
                        <label for="bik">БИК: </label>
                        <input class="form-control" name="bik" type="text" value="{{$item->bik}}"/>
                    </div>


                    <div class="form-group">
                        <label for="checking_account">Расчётный счёт: </label>
                        <input class="form-control" name="checking_account" type="text"
                               value="{{$item->checking_account}}"/>
                    </div>

                    @include('partials.buttons')
                </form>


            </div>
        </div>
        <div class="col-md-8 offset-sm-2" style="padding-top:15px; padding-bottom: 10px;margin-top: 20px;
-webkit-box-shadow: 4px 4px 8px 0px rgba(34, 60, 80, 0.2) inset;
-moz-box-shadow: 4px 4px 8px 0px rgba(34, 60, 80, 0.2) inset;
box-shadow: 4px 4px 8px 0px rgba(34, 60, 80, 0.2) inset;
">
            <h4>Платежи</h4>
            @if(!empty($payments))
                <ul style="font-size: 18px;">
                    @foreach($payments as $payment)
                        <li>
                            Внесено <b>{{$payment->money}}р.</b> Дата: <b>{{ $payment->date }}</b>
                        </li>
                    @endforeach
                </ul>
            @endif
            <br>
        </div>
    </div>





    @if (!empty($append_js))
        @foreach ($append_js as $js)
            <script src="{{ mix('/js/' . $js . '.js') }}"></script>
        @endforeach
    @endif


@endsection
