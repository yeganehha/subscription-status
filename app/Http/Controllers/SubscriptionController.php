<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\Run;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function appIndex(App $app)
    {
        return view('subscription.index' , [
           'type' => 'app',
           'object' => $app,
        ]);
    }

    public function runIndex(Run $run)
    {
        return view('subscription.index' , [
            'type' => 'run',
            'object' => $run,
        ]);
    }
}
