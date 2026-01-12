<?php

namespace Tests\Feature;

use Tests\TestCase;

class CsrfRefreshTest extends TestCase
{
    public function test_csrf_refresh_sets_token_cookie(): void
    {
        $response = $this->get('/csrf-refresh');

        $response->assertOk();
        $response->assertJsonStructure(['token']);
        $response->assertCookie('XSRF-TOKEN');
    }
}
