<?php

namespace App\Models\Payments;

class YooAddCard extends YooPayment
{
	public function __construct($organization_id) {
		$this->organization_id = $organization_id;

		$this->amount = 1;
		$this->cars = [];
		$this->detail = ['type' => 'card', 'total' => '1'];
    $this->type = 'card';
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
      'description' => '№'.$this->id.' карта ' . $this->organization->email,
      'receipt' => [
        'customer' => [
          'full_name' => $this->organization->name,
          'email' => $this->organization->email,
        ],
        'items' => [
          [
            'description' => '№'.$this->id.' карта ' . $this->organization->email,
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
      'save_payment_method' => true,
    ];
	}
}
