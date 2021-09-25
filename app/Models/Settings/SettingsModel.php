<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\CRUDFunctions;
use Illuminate\Support\Facades\Schema;

class SettingsModel extends Model
{
	use SoftDeletes;
	use CRUDFunctions;

	protected $fillable = [
	  'organization_id', 'name', 'default', 'status'
	];

	public $timestamps = false;

	public function setDefaultAttribute($value) {
		if ((int)$value === 1) TripDirectionType::query()->update(['default' => 0]);
		$this->attributes['default'] = $value;
	}

	public function organization() {
        return $this->belongsTo('App\Models\Organization');
	}
}
