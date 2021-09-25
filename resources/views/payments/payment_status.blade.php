@inject('YooPayment', 'App\Models\Payments\YooPayment')

@php
$payment = $YooPayment->where([
    'id' => $payment_id,
    'organization_id' => Auth::guard('organization')->id(),
])->first();
@endphp

@if (!is_null($payment))
    <style>
        .payment_result__icon {
            font-size: 60px;
            text-align: center;
            line-height: 52px;
        }

        .payment_result__text {
            font-size: 18px;
            text-align: center;
        }

        .payment_result__reason {
            font-size: 16px;
            display: block;
            margin-top: -3px;
            margin-bottom: 4px;
        }

        .payment_result__list {
            list-style: none;
            font-size: 15px;
            padding: 0;
        }
    </style>

    @if ($payment->status === 'succeeded')
        <div class="payment_result__icon">@include('components.icons.check-circle', ['class' => 'text-success'])</div>
        <div class="payment_result__text">
            <span>Платёж прошёл успешно</span>
        </div>
        <div style="font-size: 16px; text-align: center;">
            @if ($payment->cars_list->isEmpty())
                <div class="payment_result__list"><span>Подключена карта xxxx-xxxx-xxxx-<b>{{ $payment->card_last4 }}</b></span></div>
            @else
                <span>Оплачены автомобили:</span>
                <ul class="payment_result__list">
                    @foreach($payment->cars_list as $car)
                        <li><b>{{ $car->number }}</b></li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    @if ($payment->status === 'canceled')
        <div class="payment_result__icon">@include('components.icons.x-circle', ['class' => 'text-danger'])</div>
        <div class="payment_result__text">
            <span>Платёж отклонён</span><br/>
            <span class="payment_result__reason">Причина: {{ mb_strtolower($payment->error) }}</span>
        </div>
        <div style="font-size: 16px; text-align: center;">
            @if ($payment->cars_list->isEmpty())
                <div class="payment_result__list"><span>Карта xxxx-xxxx-xxxx-<b>{{ $payment->card_last4 }}</b> не подключена</span></div>
            @else
                <span>Автомобили, которые не были оплачены:</span>
                <ul class="payment_result__list">
                    @foreach($payment->cars_list as $car)
                        <li><b>{{ $car->number }}</b></li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    @if ($payment->status === 'pending')
        <div class="payment_result__icon">@include('components.icons.spinner')</div>
        <div class="payment_result__text">
            <span>Платёж обрабатывается</span>
        </div>

        <script>
        (function() {
            var request, response, interval;

            request = new XMLHttpRequest();

            request.onload = function(){
                response = JSON.parse(this.response);

                if (parseInt(response.id) !== {{ (int)$payment_id }}) return;

                if (response.status === 'succeeded' || response.status === 'canceled') {
                    clearInterval(interval);
                    window.location = window.location;
                }
            }

            function getPaymentStatus() {
                request.open('GET', '{{ \route('payments.get_status', ['id' => $payment_id]) }}', true);
                request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                request.send();
            }

            interval = setInterval(getPaymentStatus, 5000);
        })();
        </script>
    @endif
@endif
