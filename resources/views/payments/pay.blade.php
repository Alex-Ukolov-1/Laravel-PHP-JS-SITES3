@extends('layouts.crud')

@section('crud-content')
  <script src="https://kassa.yandex.ru/checkout-ui/v2.js"></script>

  <div class="row">
    <div class="col-sm-12">
      <h1>Оплата автомобилей</h1>
      <div id="yandex-payment-form"></div>
    </div>
  </div>

  <script>
      //Инициализация виджета. Все параметры обязательные.
      const checkout = new window.YandexCheckout({
          confirmation_token: '{{ $confirmation_token }}', // Токен, который перед проведением оплаты нужно получить от Яндекс.Кассы
          return_url: '{{ $return_url }}', // Ссылка на страницу завершения оплаты
          error_callback(error) {
              //Обработка ошибок инициализации
          }
      });

      //Отображение платежной формы в контейнере
      checkout.render('yandex-payment-form');
  </script>
@endsection
