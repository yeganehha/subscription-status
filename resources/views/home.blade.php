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
                    <hr>
                    <h3>Restful:</h3>
                    <div>
                        <strong>Document:</strong>
                        <a href="{{ route('l5-swagger.default.api') }}" target="_blank">{{ route('l5-swagger.default.api') }}</a>
                    </div>
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header">API Examples</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            @foreach($graphQls as $graphql)
                            <tr>
                                <td>
                                    <strong>Graphql</strong>
                                    <a href="{{ route('graphql.graphiql' , ['query' => $graphql['query']]) }}" target="_blank" class="mx-3">graphiql</a>
                                    <a href="{{ route('graphql' , ['query' => $graphql['query']]) }}" target="_blank">Execute</a>
                                    <div>
                                        {{ $graphql['name'] }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @foreach($restfuls as $restful)
                            <tr>
                                <td>
                                    <strong>Restful</strong>
                                    <a href="{{ $restful['link'] }}" target="_blank" class="mx-3">Execute</a>
                                    <div>
                                        {{ $restful['name'] }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Help Desk:</div>

                <div class="card-body">
                    <h3>Add New Platform:</h3>
                    <div class="mx-2">
                        For adding new platform provider just need to call:
                        <blockquote class="bg-dark bg-opacity-25 p-2 mx-3 my-1" style="border-radius: 7px;">
                            PHP artisan make:platform < name >
                        </blockquote>
                    </div>
                    <hr>
                    <h3>Run Check Subscription:</h3>
                    <div class="mx-2">
                        For Checking Subscription manually:
                        <blockquote class="bg-dark bg-opacity-25 p-2 mx-3 my-1" style="border-radius: 7px;">
                            PHP artisan queue:listen<br>
                            PHP artisan subscription:check
                        </blockquote>
                    </div>
                    <hr>
                    <h3>Always Check Subscription:</h3>
                    <div class="mx-2">
                        Add These cronjobs to server to check subscription automatically:
                        <blockquote class="bg-dark bg-opacity-25 p-2 mx-3 my-1" style="border-radius: 7px;">
                            *	*	*	*	*	/usr/local/bin/php /laravel/artisan queue:work --stop-when-empty<br>
                            0	0	*	*	*	/usr/local/bin/php /laravel/artisan subscription:check
                        </blockquote>
                    </div>
                    <hr>
                    <h3>Configuration:</h3>
                    <div class="mx-2">
                        For setting email configuration and choose day for run checking, open below file:
                        <blockquote class="bg-dark bg-opacity-25 p-2 mx-3 my-1" style="border-radius: 7px;">
                            config/subscription.php
                        </blockquote>
                        for config email server, change <strong>.env</strong> file.
                    </div>
                    <hr>
                    <h3>Unit test:</h3>
                    <div class="mx-2">
                        for run test and get result of <strong>54 Unit Test</strong>, Just need call:
                        <blockquote class="bg-dark bg-opacity-25 p-2 mx-3 my-1" style="border-radius: 7px;">
                            php artisan test
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
