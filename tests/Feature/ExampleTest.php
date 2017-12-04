<?php

namespace Tests\Feature;

use Tests\FeatureTestCase;
use App\User;

class ExampleTest extends FeatureTestCase
{
function testBasicTest()
    {
        $user = factory(User::class)->create([
            'name' => 'Alejandro Borrell',
            'email' => 'aborrell@animalear.com'
        ]);

        $this->actingAs($user, 'api')
            ->get('api/user')
            ->assertSee('Alejandro Borrell');

        /*
        $response = $this->get('/');

        $response->assertStatus(200);
        */
    }
}
