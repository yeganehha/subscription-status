<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response_on_api()
    {
        $response = $this->get('/api/document/graphiql');
        $response = $this->get('/api/graphql');
        $response->assertStatus(200);
    }
}
