<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class HouseworkerCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_admin_can_list_houseworkers(): void
    {
        // 1. Création d'un Admin et connexion via Sanctum
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        // 2. Création de 3 Houseworkers
        $houseworkers = User::factory()->count(3)->create(['role' => 'houseworker']);

        // 3. Appel de l'endpoint pour lister les Houseworkers
        $response = $this->getJson('/api/admin/houseworkers');

        // 4. Verification de la réponse
        $response->assertStatus(200)
                 ->assertJsonCount(3);

    }


    /**
     * Test : Un admin peut créer une ménagère
     */
    public function test_admin_can_create_houseworker(): void
    {
        // 1. Création d'un Admin et connexion via Sanctum
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        // 2. Données pour la nouvelle ménagère
        $payload = [
            'name' => $this->faker->name,
            'firstname' => $this->faker->firstName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => '0123456789', // <-- Ajoutez cette ligne
            'password' => 'password123',
            'role' => 'houseworker',
        ];

        // 3. Appel de l'endpoint pour créer une ménagère
        $response = $this->postJson('/api/admin/houseworkers', $payload);
        // 4. Vérification de la réponse
        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => $payload['name'],
                     'email' => $payload['email'],
                     'role' => 'houseworker',
                 ]);

        // 5. Vérification que la ménagère est bien dans la base de données
        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'role' => 'houseworker',
        ]);
    }


    /**
     * Test : La validation fonctionne (FormRequest)
     */
    public function test_validation_fails_for_invalid_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        // On envoie un tableau vide
        $response = $this->postJson('/api/admin/houseworkers', []);

        $response->assertStatus(422) // Erreur de validation
                 ->assertJsonValidationErrors(['name', 'email', 'password']);
    }


    /**
     * Test : Un utilisateur normal ne peut pas accéder (Middleware)
     */
    public function test_non_admin_cannot_access()
    {
        // On ne se connecte PAS (ou on connecte un user lambda)
        $response = $this->getJson('/api/admin/houseworkers');

        $response->assertStatus(401); // Unauthorized
    }

}
