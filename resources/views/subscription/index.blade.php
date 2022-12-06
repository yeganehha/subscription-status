@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ trans('subscription' , ['object' => $object->name]) }}
                        <a href="{{ route($type.'.index') }}" class="btn btn-info float-end">{{ __('return_back') }}</a>
                    </div>
                    <div class="card-body">
                        <livewire:subscription-table :object="$object" />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
