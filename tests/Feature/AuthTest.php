<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Đăng nhập
     *
     * @return void
     */
    public function test_signIn(): void
    {
        $response = $this->postJson('/api/auth/sign-in', ['email' => 'customer@gmail.com', 'password' => "123456"]);

        $response->assertStatus(200);
    }
}
