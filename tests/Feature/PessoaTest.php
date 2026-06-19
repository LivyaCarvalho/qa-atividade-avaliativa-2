<?php

namespace Tests\Feature;

use App\Models\Pessoa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PessoaTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_listar_pessoas(): void
    {
        $response = $this->get('/pessoas');

        $response->assertStatus(200);
    }

    public function test_deve_criar_pessoa_com_dados_validos(): void
    {
        $response = $this->post('/pessoas', [
            'name' => 'Maria Silva',
            'email' => 'maria@email.com',
            'telefone' => '32999999999',
            'matricula' => '12345',
            'password' => '123456',
            'confirmPassword' => '123456',
        ]);

        $response->assertRedirect('/pessoas');

        $this->assertDatabaseHas('pessoas', [
            'name' => 'Maria Silva',
            'email' => 'maria@email.com',
            'telefone' => '32999999999',
            'matricula' => '12345',
        ]);
    }

    public function test_nao_deve_criar_pessoa_com_senhas_diferentes(): void
    {
        $response = $this->post('/pessoas', [
            'name' => 'Joao Silva',
            'email' => 'joao@email.com',
            'telefone' => '32988888888',
            'matricula' => '54321',
            'password' => '123456',
            'confirmPassword' => '654321',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('pessoas', [
            'email' => 'joao@email.com',
        ]);
    }

    public function test_deve_atualizar_pessoa(): void
    {
        $pessoa = Pessoa::create([
            'name' => 'Pessoa Antiga',
            'email' => 'antiga@email.com',
            'telefone' => '32977777777',
            'matricula' => '111',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->put('/pessoas/' . $pessoa->id, [
            'name' => 'Pessoa Atualizada',
            'email' => 'atualizada@email.com',
            'telefone' => '32966666666',
            'matricula' => '222',
        ]);

        $response->assertRedirect('/pessoas');

        $this->assertDatabaseHas('pessoas', [
            'id' => $pessoa->id,
            'name' => 'Pessoa Atualizada',
            'email' => 'atualizada@email.com',
            'telefone' => '32966666666',
            'matricula' => '222',
        ]);
    }

    public function test_nao_deve_atualizar_pessoa_com_senhas_diferentes(): void
    {
        $pessoa = Pessoa::create([
            'name' => 'Pessoa Teste',
            'email' => 'teste@email.com',
            'telefone' => '32955555555',
            'matricula' => '333',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->put('/pessoas/' . $pessoa->id, [
            'name' => 'Pessoa Teste',
            'email' => 'teste@email.com',
            'telefone' => '32955555555',
            'matricula' => '333',
            'password' => '123456',
            'confirmPassword' => '000000',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('pessoas', [
            'id' => $pessoa->id,
            'email' => 'teste@email.com',
        ]);
    }
}
