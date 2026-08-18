<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the database to set up roles and permissions and users
        $this->seed(DatabaseSeeder::class);
    }

    public function test_the_application_redirects_guests_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_the_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $user = User::where('email', 'admin@school.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_boarding_officer_can_access_dashboard(): void
    {
        $user = User::where('email', 'boarding@school.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_bursar_can_access_dashboard(): void
    {
        $user = User::where('email', 'bursar@school.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_headteacher_can_access_dashboard(): void
    {
        $user = User::where('email', 'headteacher@school.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_user_can_update_profile_settings(): void
    {
        $user = User::where('email', 'admin@school.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->put('/profile/settings', [
            'first_name' => 'AdminUpdated',
            'last_name' => 'SchoolUpdated',
            'email' => 'admin@school.com',
            'phone' => '1234567890',
        ]);

        $response->assertStatus(302);
        $this->assertEquals('AdminUpdated', $user->fresh()->first_name);
        $this->assertEquals('SchoolUpdated', $user->fresh()->last_name);
        $this->assertEquals('1234567890', $user->fresh()->phone);
    }

    public function test_teacher_can_access_dashboard(): void
    {
        $user = User::where('email', 'grace.akinyi@school.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_matron_can_access_dashboard(): void
    {
        $user = User::where('email', 'matron@school.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_accountant_can_access_dashboard(): void
    {
        $user = User::where('email', 'accountant@school.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_procurement_officer_can_access_dashboard(): void
    {
        $user = User::where('email', 'procurement@school.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_auditor_can_access_dashboard(): void
    {
        $user = User::where('email', 'auditor@school.com')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_student_can_access_dashboard(): void
    {
        $user = User::role('Student')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }
}
