<?php

namespace Tests\Feature;

use App\Models\Autor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutorTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_listar_autores(): void
    {
        $response = $this->get('/autores');

        $response->assertStatus(200);
    }

    public function test_deve_criar_autor_com_dados_validos(): void
    {
        $response = $this->post('/autores', [
            'nome' => 'Machado de Assis',
            'nacionalidade' => 'Brasileiro',
            'data_nascimento' => '1839-06-21',
        ]);

        $response->assertRedirect('/autores');

        $this->assertDatabaseHas('autores', [
            'nome' => 'Machado de Assis',
            'nacionalidade' => 'Brasileiro',
        ]);
    }

    public function test_nao_deve_criar_autor_sem_nome(): void
    {
        $response = $this->post('/autores', [
            'nome' => '',
            'nacionalidade' => 'Brasileiro',
            'data_nascimento' => '1839-06-21',
        ]);

        $response->assertSessionHasErrors('nome');
    }

    public function test_nao_deve_criar_autor_com_data_invalida(): void
    {
        $response = $this->post('/autores', [
            'nome' => 'Autor Teste',
            'nacionalidade' => 'Brasileiro',
            'data_nascimento' => 'data-invalida',
        ]);

        $response->assertSessionHasErrors('data_nascimento');
    }

    public function test_deve_atualizar_autor(): void
    {
        $autor = Autor::create([
            'nome' => 'Autor Antigo',
            'nacionalidade' => 'Brasileiro',
        ]);

        $response = $this->put('/autores/update/' . $autor->id, [
            'nome' => 'Autor Atualizado',
            'nacionalidade' => 'Português',
            'data_nascimento' => '1901-01-01',
        ]);

        $response->assertRedirect('/autores');

        $this->assertDatabaseHas('autores', [
            'id' => $autor->id,
            'nome' => 'Autor Atualizado',
            'nacionalidade' => 'Português',
        ]);
    }
}