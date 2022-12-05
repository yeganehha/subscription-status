@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ $create ? trans('add_new' , ['object' => __('app')]) : trans('edit_object' , ['object' => $app->name]) }}
                        <a href="{{ route('app.index') }}" class="btn btn-info float-end">{{ __('return_back') }}</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ $create ? route('app.store'): route('app.update' , $app->id) }}" method="POST">
                            @csrf
                            @if ( ! $create )
                                @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-md-6 mt-2">
                                    <label for="uid" class="form-label">Unique ID</label>
                                    <input type="text" name="uid" class="form-control" id="uid" value="{{ old('uid' , ! $create  ? $app->uid : null) }}">
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="Name" class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" id="name" value="{{ old('name' , ! $create  ? $app->name : null) }}">
                                </div>
                                <div class="col-md-6 mt-2">
                                    <label for="inputPassword4" class="form-label">Provider</label>
                                    <select class="form-control" name="platform_id">
                                        @foreach($platforms as $platform)
                                            <option @selected($platform->id == old('provider' , ! $create  ? $app->platform_id : null ))
                                                    value="{{ $platform->id }}">{{ $platform->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-success mt-5" value="{{ trans('submit') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
