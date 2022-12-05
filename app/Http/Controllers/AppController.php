<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Http\Requests\StoreAppRequest;
use App\Http\Requests\UpdateAppRequest;
use App\Services\AppsService;
use App\Services\PlatformsService;

class AppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('app.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('app.edit' , [
            'create' => true ,
            'platforms' => PlatformsService::listPlatforms()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAppRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAppRequest $request)
    {
        try {
            $app = AppsService::insert($request->uid, $request->name , $request->platform_id);
            return redirect()->route('app.index')->with('success', trans('added', ['object' => $app->name]));
        } catch (\Exception $exception){
            return redirect()->back()->withErrors( trans('try_again'));
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App  $app
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(App $app)
    {
        return view('app.edit' , [
            'create' => false ,
            'app' => $app ,
            'platforms' => PlatformsService::listPlatforms()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAppRequest  $request
     * @param  \App\Models\App  $app
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAppRequest $request, App $app)
    {
        try {
            $app = AppsService::update($app , $request->uid, $request->name , $request->platform_id);
            return redirect()->route('app.index')->with('success', trans('edited', ['object' => $app->name]));
        } catch (\Exception $exception){
            return redirect()->back()->withErrors( trans('try_again'));
        }
    }
}
