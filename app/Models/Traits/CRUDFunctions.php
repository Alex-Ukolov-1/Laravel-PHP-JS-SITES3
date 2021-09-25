<?php

namespace App\Models\Traits;

use App\Repositories\StatusRepository;

trait CRUDFunctions {

	public function getStatusNameAttribute() {
		return StatusRepository::getById($this->status);
	}

	// Возвращаем список записей в виде массива вида id => name
	public function scopeList($query, $main_field_name = 'name') {
		return $query->pluck($main_field_name, 'id');
	}

	// Возвращаем список записей в виде массива вида id => name
	public function scopeFullList($query, $main_field_name = 'name') {
		return $query->pluck($main_field_name, 'id');
	}

	static function getDefaultId() {
		return null;
	}
}
