<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use OctopusTeam\Waapi\Waapi;
use Tests\TestCase;

class WaapiTest extends TestCase
{
    /** @test */
    public function it_can_send_whatsapp_message()
    {
        // Set temporary config for testing
        Config::set('waapi.base_url', 'https://waapi.example.com');
        Config::set('waapi.token', 'fake-api-token');

        // Dummy phone number for testing
        $phone   = '201234567890';
        $message = 'Hello from Waapi Test 🚀';

        // Call the method
        $response = Waapi::sendMessage($phone, $message);

        // Assert the response is not null (basic check)
        $this->assertNotNull($response);
    }
}
