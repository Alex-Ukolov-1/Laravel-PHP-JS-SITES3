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

                @php
                    if ($type === 'create') $action = route($route . '.store');
                    if ($type === 'edit') $action = route($route . '.update', $item->id);
                @endphp

                <form class="create_edit_form" method="post" action="{{ $action }}" enctype="multipart/form-data">
                    @if ($type === 'edit')
                        @method('PATCH')
                    @endif

                    @csrf

                        <div class="form-group">
                            <label for="">Заказ</label>
                            <select class="form-control contract_select" name="contract_id" id="">
                                @foreach($contracts as $contract)
                                    <option @if ((int)$contract->id === (int)$current_contract_id) selected @endif value="{{$contract['id']}}">{{$contract['name']}}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-danger driver-create-contract-btn float-right" style="margin-top:10px;">Добавить заказ</button>
                        </div>
                        <div class="driver-contract-create" style="border: solid 1px darkblue;padding:10px">
                            <h4>Детали нового заказа</h4>
                            <i class="fa fa-times close-btn" style="cursor:pointer;font-size:25px;float:right;margin-top:-25px"></i>
                            <div class="form-group">
                                <label for="">Груз</label>
                                <select class="form-control cargo_type_id-select" style="width:100%" name="cargo_type_id" data-source="cargo_types" id=""></select>
                            </div>
                            <div class="form-group">
                                <label for="distance">Расстояние рейса, км:<span class="text-danger">*</span></label>
                                <input class="form-control" name="distance" type="text" style="width:100%" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);" />
                            </div>
                            <div class="form-group">
                                <label for="">Пункт погрузки</label>
                                <select class="form-control departure_point_id-select" style="width:100%" name="departure_point_id" data-source="departure_points"  id=""></select>
                            </div>
                            <div class="form-group">
                                <label for="">Пункт разгрузки</label>
                                <select class="form-control destination_id-select" style="width:100%" name="destination_id"  data-source="destinations"  id=""></select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:36px;">
                            <label for="">Машина<span class="text-danger">*</span></label>
                            <select class="form-control" name="car_id" id="" required>
                                @foreach($cars as $car)
                                    <option @if ((int)$car->id === (int)$current_car_id) selected @endif value="{{$car['id']}}">{{$car['number']}}</option>
                                @endforeach
                            </select>
                        </div>

                    <div class="form-group">
                        <label for="loading_cargo_amount">Погрузка - Количество груза<span class="text-danger">*</span></label>
                        <input class="form-control" name="loading_cargo_amount" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);" required />
                    </div>

                    <div class="form-group">
                        <label for="unloading_cargo_amount">Разгрузка - Количество груза<span class="text-danger">*</span></label>
                        <input class="form-control" name="unloading_cargo_amount" type="text" placeholder="0.00" onkeyup="Mask.float(this);" onchange="Mask.float(this);" required />
                    </div>

                    <div class="form-group">
                        <label for="waybill">Прикрепить накладную</label>
                        <input class="d-block" type="file" name="waybills[]" multiple />
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
    <script>
        $(document).ready(function(){


            $('select[name="contract_id"]').select2({
                "language": {
                    "noResults": function(){
                        return 'Заказ не найден, нажмите <button onclick="Modal.showModal(\'.contract_id-modal\', function() { $(\'.contract_select\').select2(\'close\'); }, function() { $(\'.contract_select\').select2(\'open\'); })" type="button" class="btn btn-sm btn-success btn-cargo-add" title="Добавить заказ">Добавить</button>';
                    }
                },
                escapeMarkup: function(markup) { return markup; },
            });


            var selects_for_contract = ['cargo_type_id', 'departure_point_id', 'destination_id'];
            $(selects_for_contract).each(function(i, name){
                let source = $('select[name="'+name+'"]').attr('data-source');
                $('select[name="'+name+'"]').select2({
                    ajax: {
                        url: '/'+source+'/list',
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    },

                });
                $('select[name="'+name+'"]').on('select2:open', function (e) {
                    if (!$('.btn-cargo-add').length) {
                        $('.select2-search__field').css('width', 'calc(100% - 35px)');
                        $('.select2-search--dropdown').append('' +
                            '<button onclick="' +
                                'Modal.showModal(' +
                                    '\'.'+name+'-modal\',' +
                                    'function() {' +
                                        '$(\'select[name='+name+']\').select2(\'close\'); ' +
                                    '},' +
                                    'function() { ' +
                                        '$(\'select[name='+name+']\').select2(\'open\'); })"'+
                            'type="button" class="btn btn-sm btn-success btn-cargo-add" title="Добавить"><i class="fa fa-plus"></i></button>');
                    }
                });
            });

            $('.driver-contract-create').hide();

            $('.driver-create-contract-btn').click(function(e){
                e.preventDefault();
                $('.contract_select').parent().hide();
                $('.contract_select').val('');
                $(this).hide();
                $('.driver-contract-create').show();
                $('.driver-contract-create select, .driver-contract-create input').attr('required','required');
            });

            $('.close-btn').click(function(){
                $('.contract_select').parent().show();
                $('.contract_select').val('');

                $('.driver-contract-create').hide();
                $('.driver-contract-create input, .driver-contract-create select').val('');
                $('.driver-create-contract-btn').show();

            });

            $('.driver-create-btn').click(function(){
                $.ajax({
                    url: "/contracts",
                    type: 'post',
                    data: $('#driver-create-contract-form').serialize(),
                    success: function (data) {
                        window.location.reload();
                    },
                    error: function(data){
                        console.log(data);
                    }
                });
            });

            $('select[name="car_id"]').select2({});
        });
    </script>

    @push('modals')
        <x-select2-modal-form controller="App\Http\Controllers\ContractController" name="contract_id" sourceroute="contracts" />
    @endpush
@endsection
