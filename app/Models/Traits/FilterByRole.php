<?php

namespace App\Models\Traits;

use Auth;
use Schema;

trait FilterByRole {

	protected static function boot() {
		parent::boot();

		$user_is_admin = Auth::guard('admin')->check();
		$user_is_organization = Auth::guard('organization')->check();
		$user_is_driver = Auth::guard('driver')->check();

		if ($user_is_driver) $driver = Auth::guard('driver')->user();
		else $driver = null;

	  static::addGlobalScope('role', function ($query) use ($user_is_admin, $user_is_organization, $user_is_driver, $driver) {
	    if ($user_is_admin) return;

	    elseif ($user_is_organization) {

	    	$table_name = (new self())->getTable();

	    	$organization_id = Auth::guard('organization')->id();

	    	$query->where($table_name.'.organization_id', $organization_id);

	    } else if ($user_is_driver) {

	    	$table_name = (new self())->getTable();

	    	$organization_id = $driver->organization_id;

	    	$query->where($table_name.'.organization_id', $organization_id);

	    	$table_columns = Schema::getColumnListing($table_name);

	    	if (in_array('driver_id', $table_columns)) {
	    		$driver_id = $driver->id;
	    		$query->where($table_name.'.driver_id', $driver_id);
	    	}

	    }
	  });
	}
}
