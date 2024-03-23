<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleCalendar;

class GoogleCalendarController extends Controller
{
    protected $googleCalendar;
    public function __construct(GoogleCalendar $googleCalendar)
    {
        $this->googleCalendar = $googleCalendar;
    }

    public function store(Request $request)
    {
        $this->googleCalendar->fetchCustomerToken($request->input('code'));
        return redirect('/admin/calendar');
    }

    public function disconnect()
    {
        $this->googleCalendar->disconnect();
        return redirect('/admin/calendar');
    }
}
