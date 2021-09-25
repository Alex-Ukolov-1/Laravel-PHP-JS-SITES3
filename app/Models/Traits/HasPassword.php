<?php

namespace App\Models\Traits;

use Hash;

trait HasPassword {

    public function setPasswordAttribute($password) {
        $this->attributes['password'] = Hash::make($password);
    }

    public function setNewPasswordAttribute($password) {
        if (is_null($password) || $password === '') return;
        $this->attributes['password'] = Hash::make($password);
    }
}
