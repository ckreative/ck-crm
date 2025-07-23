<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppSettingsController extends Controller
{
    /**
     * Display the app settings main page.
     * Redirects to users page as it's the default.
     */
    public function index()
    {
        return redirect()->route('app-settings.users.index');
    }
}
