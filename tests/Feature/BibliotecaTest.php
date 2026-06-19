<?php

namespace Tests\Feature;

use App\Models\Biblioteca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BibliotecaTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_listar_bibliotecas(): void
    {
        $response = $this->get('/bibliotecas');

        $response->assertStatus(200);
    }

    public function test_deve_criar_biblioteca_com_dados_validos(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@email.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);

        $response = $this->post('/bibliotecas/create', [
            'created_by' => $user->id,
            'nome' => 'Biblioteca Central',
            'endereco' => 'Rua Principal, 100',
        ]);

        $response->assertRedirect('/bibliotecas');

        $this->assertDatabaseHas('bibliotecas', [
            'created_by' => $user->id,
            'nome' => 'Biblioteca Central',
            'endereco' => 'Rua Principal, 100',
        ]);
    }

    public function test_deve_atualizar_biblioteca(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin2@email.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);

        $biblioteca = Biblioteca::create([
            'created_by' => $user->id,
            'nome' => 'Biblioteca Antiga',
            'endereco' => 'Endereço Antigo',
        ]);

        $response = $this->put('/bibliotecas/update/' . $biblioteca->id, [
            'created_by' => $user->id,
            'nome' => 'Biblioteca Atualizada',
            'endereco' => 'Novo Endereço',
            'email' => 'biblioteca@email.com',
        ]);

        $response->assertRedirect('/bibliotecas');

        $this->assertDatabaseHas('bibliotecas', [
            'id' => $biblioteca->id,
            'nome' => 'Biblioteca Atualizada',
            'endereco' => 'Novo Endereço',
            'email' => 'biblioteca@email.com',
        ]);
    }

    public function test_deve_excluir_biblioteca(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin3@email.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);

        $biblioteca = Biblioteca::create([
            'created_by' => $user->id,
            'nome' => 'Biblioteca Excluir',
            'endereco' => 'Rua Teste',
        ]);

        $response = $this->delete('/bibliotecas/delete/' . $biblioteca->id);

        $response->assertRedirect('/bibliotecas');

        $this->assertDatabaseMissing('bibliotecas', [
            'id' => $biblioteca->id,
        ]);
    }

    public function test_retorna_404_ao_tentar_excluir_biblioteca_inexistente(): void
    {
        $response = $this->delete('/bibliotecas/delete/999');

        $response->assertStatus(404);
    }
}