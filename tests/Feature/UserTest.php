<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_listar_usuarios(): void
    {
        $response = $this->get('/users');

        $response->assertStatus(200);
    }

    public function test_deve_criar_usuario_com_dados_validos(): void
    {
        $response = $this->post('/users', [
            'name' => 'Usuario Teste',
            'email' => 'usuario@email.com',
            'password' => '123456',
        ]);

        $response->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'name' => 'Usuario Teste',
            'email' => 'usuario@email.com',
        ]);
    }

    public function test_deve_atualizar_usuario(): void
    {
        $user = User::create([
            'name' => 'Usuario Antigo',
            'email' => 'antigo@email.com',
            'password' => bcrypt('123456'),
            'role' => 'user',
        ]);

        $response = $this->put('/users/' . $user->id, [
            'name' => 'Usuario Atualizado',
            'email' => 'atualizado@email.com',
            'role' => 'admin',
        ]);

        $response->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Usuario Atualizado',
            'email' => 'atualizado@email.com',
            'role' => 'admin',
        ]);
    }

    public function test_deve_excluir_usuario(): void
    {
        $user = User::create([
            'name' => 'Usuario Excluir',
            'email' => 'excluir@email.com',
            'password' => bcrypt('123456'),
            'role' => 'user',
        ]);

        $response = $this->delete('/users/' . $user->id);

        $response->assertRedirect('/users');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_redireciona_ao_tentar_excluir_usuario_inexistente(): void
    {
        $response = $this->delete('/users/999');

        $response->assertRedirect('/users');
    }
}