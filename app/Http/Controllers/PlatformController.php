<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Http\Requests\StorePlatformRequest;
use App\Http\Requests\UpdatePlatformRequest;
use App\Services\PlatformsService;

class PlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('platform.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('platform.edit' , [
            'create' => true ,
            'providers' => PlatformsService::listProviders()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePlatformRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePlatformRequest $request)
    {
        try {
            $platform = PlatformsService::insert($request->name, $request->provider);
            return redirect()->route('platform.index')->with('success', trans('added', ['object' => $platform->name]));
        } catch (\Exception $exception){
            return redirect()->back()->withErrors( trans('try_again'));
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Platform  $platform
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Platform $platform)
    {
        return view('platform.edit' , [
            'create' => false ,
            'platform' => $platform ,
            'providers' => PlatformsService::listProviders()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdatePlatformRequest $request
     * @param \App\Models\Platform $platform
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(UpdatePlatformRequest $request, Platform $platform)
    {
        try {
            $platform = PlatformsService::update($platform , $request->name, $request->provider);
            return redirect()->route('platform.index')->with('success', trans('edited', ['object' => $platform->name]));
        } catch (\Exception $exception){
            return redirect()->back()->withErrors( trans('try_again'));
        }
    }

}
