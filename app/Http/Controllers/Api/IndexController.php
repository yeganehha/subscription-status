<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Platform;
use App\Models\Run;
use App\Services\AppsService;
use App\Services\CheckService;
use App\Services\PlatformsService;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function platforms(Request $request)
    {
        $platforms = PlatformsService::searchPlatforms($request->id , $request->name , $request->page);
        return response($platforms);
    }

    public function apps(Request $request,Platform $platform)
    {
        $apps = AppsService::search($request->id , $request->uid, $request->name, $request->status , $platform->id , $request->page, null , ['platform']);
        return response($apps);
    }

    public function appSubscription(Request $request,Platform $platform,App $app)
    {
        $subscriptions = CheckService::searchSubscription($request->id , null, $request->status , $app->id , null , $request->page, null , ['run' , 'app', 'app.platform']);
        return response($subscriptions);
    }

    public function rounds(Request $request)
    {
        $rounds = CheckService::searchRuns($request->id, null ,false , null  , $request->page);
        return response($rounds);
    }

    public function lastRound(Request $request)
    {
        $rounds = CheckService::searchRuns($request->id, null ,true , null  ,false);
        return response($rounds);
    }

    public function runSubscription(Request $request, Run $run)
    {
        $subscriptions = CheckService::searchSubscription($request->id , null, $request->status ,null , $run->id , $request->page, null , ['run' , 'app', 'app.platform']);
        return response($subscriptions);
    }

}
