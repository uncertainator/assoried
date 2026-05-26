<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;

class MaintenanceController extends Controller
{
    public function toggle(): RedirectResponse
    {
        $current = (bool) Setting::get('maintenance_mode', false);
        Setting::set('maintenance_mode', $current ? '0' : '1');
        $label = $current ? 'désactivé' : 'activé';

        return back()->with('success', "Mode maintenance {$label}.");
    }
}
