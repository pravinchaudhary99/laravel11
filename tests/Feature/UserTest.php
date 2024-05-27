<?php

use App\Models\User;

test('user create', function () {
    $user = User::factory()->create();

    $response = $this->get('/');

    $response->assertStatus(200);

    expect(User::where('email', $user->email)->exists())->toBeTrue();
    
});
