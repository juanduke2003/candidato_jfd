<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use App\Models\Candidato;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTFactory;
use Illuminate\Support\Facades\Auth;

use Tests\TestCase;

class CandidatoTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    public function testCrearCandidatoComoManager()
    {
        $user = User::find(1);
        $token = JWTAuth::fromUser($user);

        /*$tokenExpiration = now()->addDay(1); // Token expira en 1 día

        $payload = JWTFactory::sub(Auth::user()->id)
            ->aud('api')
            ->expiresAt($tokenExpiration)
            ->make();

        $token = JWTAuth::encode($payload)->get();*/

        $data = [
            'name' => 'John Doe',
            'source' => 'LinkedIn',
            'owner' => $user->id,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->post('/api/v1/lead', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('candidatos', $data);
    }

    public function testCrearCandidatoComoAgente()
    {
        $user = User::find(2);
        $token = JWTAuth::fromUser($user);

        $data = [
            'name' => 'Jane Smith',
            'source' => 'Indeed',
            'owner' => $user->id,
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->post('/api/v1/lead', $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('candidatos', $data);
    }

    public function testObtenerCandidatosComoManager()
    {
        $user = User::find(1);
        $token = JWTAuth::fromUser($user);

        //$candidatos = factory(Candidato::class, 5)->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->get('/api/v1/leads');

        $response->assertStatus(200);
    }

    public function testObtenerCandidatosComoAgente()
    {
        $user = User::find(2);
        $token = JWTAuth::fromUser($user);

        //$candidatos = Candidato::find(2);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->get('/api/v1/leads');

        $response->assertStatus(200);
    }

    public function testObtenerCandidatoEspecifico()
    {
        $user = User::find(1);
        $token = JWTAuth::fromUser($user);

        $candidato = Candidato::find(2);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->get('/api/v1/lead/' . $candidato->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $candidato->id,
                    'name' => $candidato->name,
                    'source' => $candidato->source,
                    'owner' => $candidato->owner,
                ],
            ]);
    }
}
