<?php

namespace Tests\Feature;

use Tests\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Models\Guest;

class GuestTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreateGuest()
    {
        $payload = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+79001234567',
        ];

        $response = $this->json('POST', '/api/guests', $payload);

        $response->seeStatusCode(201)
            ->seeJson([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+79001234567',
                'country' => 'RU' // Код страны, если ваше приложение возвращает код
            ]);
    }

    public function testShowGuest()
    {
        $guest = Guest::create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'phone' => '+4915112345678',
            'country' => 'DE', // Код страны вместо полного названия
        ]);

        $response = $this->json('GET', '/api/guests/' . $guest->id);

        $response->seeStatusCode(200)
            ->seeJson([
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane.doe@example.com',
                'phone' => '+4915112345678',
                'country' => 'DE'
            ]);
    }

    public function testUpdateGuest()
    {
        $guest = Guest::create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'email' => 'alice.smith@example.com',
            'phone' => '+447911123456',
            'country' => 'GB', // Код страны вместо полного названия
        ]);

        $payload = [
            'first_name' => 'Alicia',
            'country' => 'GB' // Код страны
        ];

        $response = $this->json('PUT', '/api/guests/' . $guest->id, $payload);

        $response->seeStatusCode(200)
            ->seeJson([
                'first_name' => 'Alicia',
                'last_name' => 'Smith',
                'email' => 'alice.smith@example.com',
                'phone' => '+447911123456',
                'country' => 'GB'
            ]);
    }

    public function testDeleteGuest()
    {
        $guest = Guest::create([
            'first_name' => 'Bob',
            'last_name' => 'Brown',
            'email' => 'bob.brown@example.com',
            'phone' => '+33123456789',
            'country' => 'FR', // Код страны
        ]);

        $response = $this->json('DELETE', '/api/guests/' . $guest->id);

        $response->seeStatusCode(200)
                 ->seeJson(['message' => 'Guest deleted']);

        $response = $this->json('GET', '/api/guests/' . $guest->id);
        $response->seeStatusCode(404);
    }
}
