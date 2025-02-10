<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_contact()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'message' => 'Hello, this is a test message.',
        ];

        $response = $this->postJson('/api/contacts', $data);

        $response->assertStatus(201)
                 ->assertJson($data);
    }

    public function test_can_update_contact()
    {
        $contact = Contact::factory()->create();

        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $response = $this->putJson("/api/contacts/{$contact->id}", $data);

        $response->assertStatus(200)
                 ->assertJson($data);
    }

    public function test_can_delete_contact()
    {
        $contact = Contact::factory()->create();

        $response = $this->deleteJson("/api/contacts/{$contact->id}");

        $response->assertStatus(204);
    }
}