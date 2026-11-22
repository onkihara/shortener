<?php

use App\Models\User;

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('shows the hero and a link to the separate create page to guests', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('id="heroApp"', false)
        ->assertSee('data-create-route="'.route('shortener.create-form').'"', false)
        ->assertDontSee('id="createFormApp"', false);

    $this->get(route('shortener.create-form'))
        ->assertOk()
        ->assertSee('id="createFormApp"', false)
        ->assertDontSee('id="heroApp"', false);
});

it('redirects authenticated users to the create page without the hero', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/')
        ->assertRedirect(route('shortener.create-form'));

    $this->get(route('shortener.create-form'))
        ->assertOk()
        ->assertDontSee('id="heroApp"', false)
        ->assertSee('id="createFormApp"', false);
});
