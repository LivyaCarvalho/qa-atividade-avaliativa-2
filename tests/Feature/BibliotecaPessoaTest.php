<?php

namespace Tests\Feature;

use App\Models\Biblioteca;
use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BibliotecaPessoaTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_associar_pessoa_a_biblioteca(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@email.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);

        $biblioteca = Biblioteca::create([
            'created_by' => $user->id,
            'nome' => 'Biblioteca Central',
            'endereco' => 'Rua Principal',
        ]);

        $pessoa = Pessoa::create([
            'name' => 'Maria Silva',
            'email' => 'maria@email.com',
            'password' => bcrypt('123456'),
            'matricula' => '123',
            'telefone' => '32999999999',
        ]);

        $response = $this->post('/bibliotecas/' . $biblioteca->id . '/pessoas', [
            'pessoa_id' => $pessoa->id,
        ]);

        $response->assertRedirect('/bibliotecas/edit/' . $biblioteca->id);

        $this->assertDatabaseHas('biblioteca_pessoa', [
            'biblioteca_id' => $biblioteca->id,
            'pessoa_id' => $pessoa->id,
        ]);
    }

    public function test_nao_deve_associar_pessoa_inexistente_a_biblioteca(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin2@email.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);

        $biblioteca = Biblioteca::create([
            'created_by' => $user->id,
            'nome' => 'Biblioteca Central',
            'endereco' => 'Rua Principal',
        ]);

        $response = $this->post('/bibliotecas/' . $biblioteca->id . '/pessoas', [
            'pessoa_id' => 999,
        ]);

        $response->assertSessionHasErrors('pessoa_id');

        $this->assertDatabaseMissing('biblioteca_pessoa', [
            'biblioteca_id' => $biblioteca->id,
            'pessoa_id' => 999,
        ]);
    }

    public function test_nao_deve_associar_a_mesma_pessoa_duas_vezes(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin3@email.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);

        $biblioteca = Biblioteca::create([
            'created_by' => $user->id,
            'nome' => 'Biblioteca Central',
            'endereco' => 'Rua Principal',
        ]);

        $pessoa = Pessoa::create([
            'name' => 'João Silva',
            'email' => 'joao@email.com',
            'password' => bcrypt('123456'),
            'matricula' => '456',
            'telefone' => '32988888888',
        ]);

        $this->post('/bibliotecas/' . $biblioteca->id . '/pessoas', [
            'pessoa_id' => $pessoa->id,
        ]);

        $response = $this->post('/bibliotecas/' . $biblioteca->id . '/pessoas', [
            'pessoa_id' => $pessoa->id,
        ]);

        $response->assertRedirect('/bibliotecas/' . $biblioteca->id . '/pessoas/add');

        $this->assertEquals(1, $biblioteca->pessoas()->where('pessoa_id', $pessoa->id)->count());
    }
}