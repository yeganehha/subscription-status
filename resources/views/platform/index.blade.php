@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('platforms') }}
                        <a href="{{ route('platform.create') }}" class="btn btn-success float-end">{{ __('add_new' , ['object' => __('platform')]) }}</a>
                    </div>
                    <div class="card-body">
                        <livewire:platform-table />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
