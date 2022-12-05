@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('apps') }}
                        <a href="{{ route('app.create') }}" class="btn btn-success float-end">{{ __('add_new' , ['object' => __('app')]) }}</a>
                    </div>
                    <div class="card-body">
                        <livewire:app-table />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
