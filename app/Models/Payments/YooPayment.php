<?php

namespace App\Models\Payments;

class YooPayment extends Payment
{
	const STATUSES = [
		'pending' => 'Создан',
		'waiting_for_capture' => 'Ожидание списания',
		'succeeded' => 'Завершён',
		'canceled' => 'Отменён',
	];

	const ERRORS = [
		'3d_secure_failed' => 'Не пройдена аутентификация по 3-D Secure',
		'call_issuer' => 'Оплата данным платёжным средством отклонена по неизвестным причинам',
		'canceled_by_merchant' => 'Платёж отменен по API при оплате в две стадии',
		'card_expired' => 'Истёк срок действия банковской карты',
		'country_forbidden' => 'Нельзя заплатить банковской картой, выпущенной в этой стране',
		'expired_on_capture' => 'Истёк срок списания оплаты у двухстадийного платежа',
		'expired_on_confirmation' => 'Оплата не произведена',
		'fraud_suspected' => 'Платёж заблокирован из-за подозрения в мошенничестве',
		'general_decline' => 'Причина неизвестна',
		'identification_required' => 'Превышены ограничения на платежи для кошелька',
		'insufficient_funds' => 'Недостаточно денег для оплаты',
		'internal_timeout' => 'Технические неполадки на стороне сервера',
		'invalid_card_number' => 'Неправильно указан номер карты',
		'invalid_csc' => 'Неправильно указан код CVV2 (CVC2, CID)',
		'issuer_unavailable' => 'Организация, выпустившая платёжное средство, недоступна',
		'payment_method_limit_exceeded' => 'Исчерпан лимит платежей для данного платёжного средства',
		'payment_method_restricted' => 'Запрещены операции данным платёжным средством',
		'permission_revoked' => 'Пользователь отозвал разрешение на автоплатежи',
	];

	protected $table = 'yoo_payments';

	protected $fillable = [
		'organization_id', 'payment_id', 'card_last4', 'type', 'months', 'amount', 'status', 'cars', 'detail', 'cancellation_party', 'cancellation_reason', 'comment', 'created_manually'
	];

	protected $hidden = [
	  'payment_id',
	];

	static public function paymentID($payment_id) {
		return self::where('payment_id', $payment_id)->first();
	}

	public function setCanceledStatus($cancellation_party, $cancellation_reason) {
		$this->update([
		  'status' => 'canceled',
		  'cancellation_party' => $cancellation_party,
		  'cancellation_reason' => $cancellation_reason,
		]);

		if ($this->type === 'renew') {
			$this->resetCars();
		}

		foreach ($this->cars_list as $car) {
			$car->update([
				'last_payment_error' => $cancellation_reason,
			]);
		}
	}

	public function organization() {
		return $this->belongsTo('App\Models\Organization');
	}

	public function getErrorAttribute() {
		return self::ERRORS[$this->cancellation_reason] ?? '';
	}

	public function getStatusHrExtendedAttribute() {
		if ($this->status === 'canceled' && (!empty($this->cancellation_party) || !empty($this->cancellation_reason))) {
			return $this->status_hr . ' (' . ($this->cancellation_party ?? '-') . ' | ' . ($this->cancellation_reason ?? '-') . ')';
		} else {
			return $this->status_hr;
		}
	}

	public function setCreatedManually($value) {
		$this->created_manually = $value;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function setComment($comment) {
		$this->comment = $comment;
	}

	public function getPaymentDataForYooKassa() {
		return [
      'amount' => [
        'value' => $this->amount,
        'currency' => 'RUB',
      ],
      'confirmation' => [
        'type' => 'embedded',
      ],
      'capture' => true,
      'description' => '№'.$this->id.' оплата ' . $this->organization->email,
      'receipt' => [
        'customer' => [
          'full_name' => $this->organization->name,
          'email' => $this->organization->email,
        ],
        'items' => [
          [
            'description' => '№'.$this->id.' оплата ' . $this->organization->email,
            'quantity' => '1.00',
            'amount' => [
              'value' => $this->amount,
              'currency' => 'RUB'
            ],
            'vat_code' => '2',
            'payment_mode' => 'full_prepayment',
            'payment_subject' => 'commodity'
          ]
        ]
      ],
    ];
	}

	public $custom_fields = ['status_hr', 'status_hr_extended', 'detail_hr'];
}
