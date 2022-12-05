@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">API Documents</div>

                <div class="card-body">
                    <h3>Graphql:</h3>
                    <div>
                        <strong>Graphiql (Query Builder):</strong>
                        <a href="{{ route('graphql.graphiql') }}" target="_blank">{{ route('graphql.graphiql') }}</a>
                    </div>
                    <div>
                        <strong>Graphql URI:</strong>
                        <a href="{{ route('graphql') }}" target="_blank">{{ route('graphql') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
