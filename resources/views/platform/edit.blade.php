@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ $create ? trans('add_new' , ['object' => __('platforms')]) : trans('edit_object' , ['object' => $platform->name]) }}
                        <a href="{{ route('platform.index') }}" class="btn btn-info float-end">{{ __('return_back') }}</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ $create ? route('platform.store'): route('platform.update' , $platform->id) }}" method="POST">
                            @csrf
                            @if ( ! $create )
                                @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="Name" class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" id="name" value="{{ old('name' , ! $create  ? $platform->name : null) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="inputPassword4" class="form-label">Provider</label>
                                    <select class="form-control" name="provider">
                                        @foreach($providers as $provider)
                                            <option @selected($provider == old('provider' , ! $create  ? $platform->provider : null ))
                                                    value="{{ $provider }}">{{ $provider }}</option>
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
