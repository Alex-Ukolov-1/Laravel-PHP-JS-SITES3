<?php

namespace App\Http\Controllers;

use Auth;
use Session;

class AuthController
{
    public function loginAs(string $type, int $id) {
        if (!Auth::guard('admin')->check()) {
            abort(403);
        }

        Session::put('admin_id', Auth::guard('admin')->id());

        if ($type === 'organization') {
            Auth::guard('organization')->loginUsingId($id);
            Auth::guard('driver')->logout();
        } elseif ($type === 'driver') {
            Auth::guard('driver')->loginUsingId($id);
            Auth::guard('organization')->logout();
        }

        Auth::guard('admin')->logout();

        return redirect()->route('trips.index');
    }

    public function loginAsDriver(int $id)
    {
        return $this->loginAs('driver', $id);
    }

    public function loginAsOrganization(int $id)
    {
        return $this->loginAs('organization', $id);
    }

    public function returnToAdmin()
    {
        if (!Session::has('admin_id')) {
            abort(403);
        }

        if (Auth::guard('organization')->check()) {
            $return_route_name = 'organizations.index';
        } elseif (Auth::guard('driver')->check()) {
            $return_route_name = 'trips.index';
        }

        $admin_id = Session::get('admin_id');

        Session::forget('admin_id');

        Auth::guard('admin')->loginUsingId($admin_id);
        Auth::guard('organization')->logout();
        Auth::guard('driver')->logout();

        return redirect()->route($return_route_name);
    }
}
