<?php

namespace App\Http\Controllers\Payments;

use Auth;
use App\Models\Organization;
use App\Models\Car;
use App\Models\Payments\YooPayment;
use App\Models\Payments\YooPaymentData;
use YandexCheckout\Client as YandexKassa;
use Mail;
use Log;

class YooPaymentController
{

  public function pay($organization_id = null, $payment_method_id = null, $car_ids = null, $return_url = null, $type = 'payment') {

    if (is_null($organization_id)) {
      $organization = Auth::guard('organization')->user();
    } else {
      $organization = Organization::findOrFail($organization_id);
    }

    // Добавляем в платёж либо сервисы переданные в функцию, либо переданные в http-запросе, либо неоплаченные
    if ($car_ids !== null) {
      $cars = Car::whereIn('id', $car_ids)->get();
    } else {
      $cars_from_request = $this->getCarsFromRequest();

      if (!empty($cars_from_request)) {
        $cars = $cars_from_request;
      } else {
        $cars = $organization->getUnpaidCars();
      }
    }

    $yoopayment = new YooPayment([ 'organization_id' => $organization->id ]);
    $yoopayment->addCars($cars);
    $yoopayment->setType($type);

    if ((int)$yoopayment->amount === 0) {
      $yoopayment->activateCars();
      return $return_url ? \redirect($return_url) : \back();
    }

    $yoopayment->save();

    $payment_data = $yoopayment->getPaymentDataForYooKassa();

    if (!is_null($payment_method_id)) {
      $payment_data['payment_method_id'] = $payment_method_id;
      unset($payment_data['confirmation']);
    }

    if (!is_null($organization->payment_data) && (string)$organization->payment_data->auto_renew === '1') $payment_data['save_payment_method'] = true;
    if (\request()->has('auto_renew_checkbox') && \request()->get('auto_renew_checkbox') === '1') $payment_data['save_payment_method'] = true;

    try {
      $kassa = new YandexKassa();
      $kassa->setAuth(env('YANDEX_KASSA_ID'), env('YANDEX_KASSA_KEY'));

      $payment = $kassa->createPayment($payment_data, uniqid('', true));
    } catch(\Exception $e) {
      $code = $e->getCode();
      $message = $e->getMessage();

      $this->log("Ошибка {$code}: {$message}");
      return \back()->withErrors('Автоматическое продление недоступно');      
    }

    $yoopayment->update([
      'payment_id' => $payment->getId(),
      'status' => $payment->getStatus(),
    ]);

    if (is_null($payment_method_id)) {
      return view('payments.pay', [
        'confirmation_token' => $payment->getConfirmation()->confirmationToken,
        'return_url' => ($return_url ?? \route('cars.index')) . '?payment_id=' . $yoopayment->id,
        'template' => $this,
      ]);
    }
  }

  public function return() {
    return view('payments.return', ['template' => $this]);
  }

  private function log($message) {
    Log::channel('payments')->info($message);
  }

  public function refund($payment) {
    $kassa = new YandexKassa();
    $kassa->setAuth(env('YANDEX_KASSA_ID'), env('YANDEX_KASSA_KEY'));

    $refund = $kassa->createRefund(
      array(
        'amount' => array(
          'value' => 1,
          'currency' => 'RUB',
        ),
        'payment_id' => $payment->getId(),
      ),
      uniqid('', true)
    );
  }

  public function yooKassaNotificationHandler() {
    $data = \request()->all();

    $this->log('Событие: ' . $data['event'] . ' ID: ' . ($data['object']['id'] ?? '' ));

    $is_refund = $data['event'] === 'refund.succeeded';
    $is_not_payment = explode('.', $data['event'])[0] !== 'payment';
    $has_no_id = empty($data['object']['id']);

    // Если это уведомление о возврате, или о чём-то другом, но не о платеже, либо у него нет id, то ничего не делаем
    if ($is_refund || $is_not_payment || $has_no_id) {
      return;
    }

    $kassa = new YandexKassa();
    $kassa->setAuth(env('YANDEX_KASSA_ID'), env('YANDEX_KASSA_KEY'));

    try {
      $yandex_payment = $kassa->getPaymentInfo($data['object']['id']);
    } catch (\Exception $e) {
      if ($data['event'] === 'payment.canceled') {
        $payment = YooPayment::paymentID($data['object']['id']);

        if (!is_null($payment)) {
          $cancellation_party = $data['object']['cancellation_details']['party'];
          $cancellation_reason = $data['object']['cancellation_details']['reason'];

          $payment->setCanceledStatus($cancellation_party, $cancellation_reason);

          $this->log('Платёж отменён. ID: ' . $payment->payment_id . ' Причина: ' . $cancellation_party . ' \ ' . $cancellation_reason);
        } else {
          $this->log('Неверный ID платежа. ID: ' . $data['object']['id']);
        }
      }

      exit();
    }

    $payment = YooPayment::paymentID($yandex_payment->getId());

    if (is_null($payment)) {
      $this->log('Платёж не найден в базе сайта. ID: ' . $yandex_payment->getId());
      return;
    }

    $yandex_payment_method = $yandex_payment->getPaymentMethod();

    if (method_exists($yandex_payment_method, 'getLast4')) {
      $last4 = $yandex_payment_method->getLast4();
    } else {
      $last4 = null;
    }

    $payment->update([
      'status' => $yandex_payment->getStatus(),
      'card_last4' => $last4,
    ]);

    // Если платёж был отменён
    if ($yandex_payment->getStatus() === 'canceled') {
      $cancellation_party = $yandex_payment->getCancellationDetails()->getParty();
      $cancellation_reason = $yandex_payment->getCancellationDetails()->getReason();

      $payment->setCanceledStatus($cancellation_party, $cancellation_reason);

      $this->log('Платёж отменён. ID: ' . $payment->payment_id . ' Причина: ' . $cancellation_party . ' \ ' . $cancellation_reason);

      exit();
    }

    // Все последующие действия выполняются только если платёж прошёл успешно
    if ($yandex_payment->getStatus() !== 'succeeded') return;

    // Подключаем оплаченные услуги
    $payment->activateCars();

    if (is_null($payment->organization)) {
      $this->log('Не найдена организация, совершившая платёж. ID организации: ' . $payment->organization_id . ' ID платежа: ' . $yandex_payment->getId());
      return;
    }

    $organization = $payment->organization;

    // Если у пользователя уже есть сохранённые платёжные данные, то обновляем их. Если нет, то создаём
    if (!is_null($organization->payment_data)) {
      $payment_data = $organization->payment_data;
    } else {
      $payment_data = new YooPaymentData;
      $payment_data->organization_id = $organization->id;
    }

    $yandex_payment_method = $yandex_payment->getPaymentMethod();

    // Если пользователь поставил галочку "Привязать карту к магазину"
    if ($yandex_payment_method->getSaved() === true) {

      if (method_exists($yandex_payment_method, 'getLast4')) {
        $last4 = $yandex_payment_method->getLast4();
      } else {
        $last4 = null;
      }

      $payment_data->yandex_payment_method_id = $yandex_payment_method->getId();
      $payment_data->card_last4 = $last4;
      $payment_data->auto_renew = 1;
    }

    $payment_data->save();

    // Если у платежа нет автомобилей, значит это платёж для подключения карты и его нужно вернуть
    if (empty($payment->cars)) {
      $this->refund($yandex_payment);
      return;
    }

    $this->log('Автомобили оплачены. ID организации: ' . $payment_data->organization_id . ' ID платежа: ' . $yandex_payment->getId());

    if (!empty($organization->email)) {
      try {
        Mail::send('emails.registration_request', ['title' => env('MAIL_FROM_NAME') . ' | Оплата автомобилей', 'msg' => 'Автомобили успешно оплачены'], function ($m) use ($organization) {
            $m->to($organization->email)->subject(env('MAIL_FROM_NAME') . ' | Оплата автомобилей');
        });
      } catch(\Exception $e) {}
    }

  }

  public function getCarsFromRequest(): array {
    $car_ids = \request()->get('ids');

    $organization = Auth::guard('organization')->user();

    if (empty($car_ids) || empty($organization)) return [];

    $cars = [];

    if (!empty($car_ids)) {
      foreach ($car_ids as $car_id) {
        $car = Car::find($car_id);

        if ($car) {
          $cars[] = $car;
        }
      }
    }

    return $cars;
  }

  public function getPaymentStatus($payment_id) {
    $payment = YooPayment::where([
        'id' => $payment_id,
        'organization_id' => Auth::guard('organization')->id(),
    ])->first();

    if (is_null($payment)) return;
    
    return [
      'id' => $payment->id,
      'status' => $payment->status,
    ];
  }

}
