<?php

namespace App\Http\Controllers\Admin;
use Gate;
use Illuminate\Http\Response;
class HomeController
{
    public function index()
    {
        if(Gate::denies('admin_dashboard_access')){
            return redirect()->route('admin.dashboard');
        }
           return view('admin.home');
    }
    public function userDashboard()
    {
        abort_if(Gate::denies('user_dashboard_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.user_dashboard',[
            'currentSubscription' => auth()->user()->subscription
        ]);
    }
}
