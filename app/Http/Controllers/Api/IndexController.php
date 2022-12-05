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
use OpenApi\Annotations as OA;

class IndexController extends Controller
{
    /**
     * Get List of all platforms
     * @OA\Get (
     *     path="/api/rest/platforms",
     *     tags={"Platform"},
     *     @OA\Parameter(
     *         in="query",
     *         name="id",
     *         required=false,
     *         description="Id Of Special platform",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="name",
     *         required=false,
     *         description="full or part of platform name for search by name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         description="Page Number",
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *     )
     * )
     */
    public function platforms(Request $request)
    {
        $platforms = PlatformsService::searchPlatforms($request->id , $request->name , $request->page);
        return response($platforms);
    }

    /**
     * Get List of special platform's application
     * @OA\Get (
     *     path="/api/rest/platforms/{platform}/apps",
     *     tags={"Platform" , "Application" },
     *     @OA\Parameter(
     *         in="path",
     *         name="platform",
     *         required=true,
     *         description="Id Of platform",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="id",
     *         required=false,
     *         description="Id Of Special app",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="uid",
     *         required=false,
     *         description="Unique Id Of Special app",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="name",
     *         required=false,
     *         description="full or part of platform name for search by name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="status",
     *         required=false,
     *         description="filter by status (status: active-pending-expired)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         description="Page Number",
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *     )
     * )
     */
    public function apps(Request $request,Platform $platform)
    {
        $apps = AppsService::search($request->id , $request->uid, $request->name, $request->status , $platform->id , $request->page, null , ['platform']);
        return response($apps);
    }



    /**
     * Get List of application subscription history
     * @OA\Get (
     *     path="/api/rest/platforms/{platform}/app/{app}/subscriptions",
     *     tags={"Platform" , "Application" , "Subscription" },
     *     @OA\Parameter(
     *         in="path",
     *         name="platform",
     *         required=true,
     *         description="Id Of platform",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         in="path",
     *         name="app",
     *         required=true,
     *         description="Id Of Application",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="id",
     *         required=false,
     *         description="Id Of Special subscription",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="status",
     *         required=false,
     *         description="filter by status (status: active-pending-expired)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         description="Page Number",
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *     )
     * )
     */
    public function appSubscription(Request $request,Platform $platform,App $app)
    {
        $subscriptions = CheckService::searchSubscription($request->id , null, $request->status , $app->id , null , $request->page, null , ['run' , 'app', 'app.platform']);
        return response($subscriptions);
    }



    /**
     * Get all rounds of checking subscription
     * @OA\Get (
     *     path="/api/rest/rounds",
     *     tags={"Round" },
     *     @OA\Parameter(
     *         in="query",
     *         name="id",
     *         required=false,
     *         description="Id Of Special Round",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         description="Page Number",
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *     )
     * )
     */
    public function rounds(Request $request)
    {
        $rounds = CheckService::searchRuns($request->id, null ,false , null  , $request->page);
        return response($rounds);
    }




    /**
     * Get last round of checking subscription
     * @OA\Get (
     *     path="/api/rest/rounds/last",
     *     tags={"Round" },
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *     )
     * )
     */
    public function lastRound(Request $request)
    {
        $rounds = CheckService::searchRuns(null, null ,true , null  ,false);
        return response($rounds);
    }



    /**
     * Get List of round subscription history
     * @OA\Get (
     *     path="/api/rest/rounds/{run}/subscriptions",
     *     tags={"Round" , "Subscription" },
     *     @OA\Parameter(
     *         in="path",
     *         name="run",
     *         required=true,
     *         description="Id Of Round",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="id",
     *         required=false,
     *         description="Id Of Special subscription",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="status",
     *         required=false,
     *         description="filter by status (status: active-pending-expired)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="page",
     *         required=false,
     *         description="Page Number",
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *     )
     * )
     */
    public function runSubscription(Request $request, Run $run)
    {
        $subscriptions = CheckService::searchSubscription($request->id , null, $request->status ,null , $run->id , $request->page, null , ['run' , 'app', 'app.platform']);
        return response($subscriptions);
    }

}
