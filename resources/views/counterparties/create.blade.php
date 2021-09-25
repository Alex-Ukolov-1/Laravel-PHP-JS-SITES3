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

                <form class="create_edit_form" method="post" action="{{route($route . '.store')}}">
                    @csrf

                    @include('partials.buttons')

                    <div class="form-group">
                        <label for="name">Название: <span class="text-danger">*</span></label>
                        <input class="form-control" required="required" name="name[]" type="text" value=""/>
                    </div>

                    <div class="form-group">
                        <label for="email">E-Mail: </label>
                        <input class="form-control" name="email[]" type="text" value=""/>
                    </div>

                    <div class="form-group">
                        <label for="phone">Телефон: </label>
                        <input class="form-control" name="phone[]" type="text" value=""/>
                    </div>

                    <div class="form-group">
                        <label for="note">Примечание: </label>
                        <textarea cols="30" rows="3" class="form-control note" name="note[]"></textarea>
                    </div>

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
                            <option value="" style="display:none;"></option>
                            <option value="1">Заказчик</option>
                            <option value="2">Поставщик</option>
                            <option value="3">Исполнитель</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="inn">ИНН: </label>
                        <input class="form-control" name="inn" type="text" value=""/>
                    </div>


                    <div class="form-group">
                        <label for="bik">БИК: </label>
                        <input class="form-control" name="bik" type="text" value=""/>
                    </div>


                    <div class="form-group">
                        <label for="checking_account">Расчётный счёт: </label>
                        <input class="form-control" name="checking_account" type="text" value=""/>
                    </div>

                    <div class="form-group">
                        <label for="status">Статус:<span class="text-danger">*</span> </label>

                        <label style="margin-left: 1rem;" class="custom-control custom-radio custom-control-inline">
                            <input type="radio" name="status" class="custom-control-input" value="1" required="required"
                                   checked="checked">
                            <span class="custom-control-label">Включен</span>
                        </label>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" name="status" class="custom-control-input" value="0"
                                   required="required">
                            <span class="custom-control-label">Отключен</span>
                        </label>
                    </div>

                    <div class="float-right">
                        @foreach ($form_buttons as $form_button_name)
                            @include('components.form_buttons.'.$form_button_name)
                        @endforeach
                    </div>
                    @include('partials.buttons')
                </form>


            </div>
        </div>
    </div>

    @if (!empty($append_js))
        @foreach ($append_js as $js)
            <script src="{{ mix('/js/' . $js . '.js') }}"></script>
        @endforeach
    @endif

@endsection
