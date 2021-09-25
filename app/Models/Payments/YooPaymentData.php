<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;

class YooPaymentData extends Model
{
	protected $table = 'yoo_payment_data';
	public $timestamps = false;

	protected $fillable = [
		'organization_id', 'auto_renew', 'yoomoney_payment_method_id', 'card_last4',
	];

	protected $hidden = [
		'yandex_payment_method_id',
	];

	public function getIsAutoRenewAvailableAttribute() {
		if (!empty($this->yandex_payment_method_id)) return true;
		else return false;
	}

	public function getAutoRenewHrAttribute() {
		if ($this->is_auto_renew_available === true && (string)$this->auto_renew === '1') return 'Включено';
		else return 'Отключено';
	}

	public function organization() {
		return $this->belongsTo('App\Models\Organization');
	}

	public $custom_fields = ['auto_renew_hr', 'is_auto_renew_available'];
}
