<?php

namespace Tests\Feature;

use App\Models\Autor;
use App\Models\Livro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivroTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_listar_livros(): void
    {
        $response = $this->get('/livros');

        $response->assertStatus(200);
    }

    public function test_deve_criar_livro_com_dados_validos(): void
    {
        $autor = Autor::create([
            'nome' => 'Machado de Assis',
            'nacionalidade' => 'Brasileiro',
        ]);

        $response = $this->post('/livros', [
            'titulo' => 'Dom Casmurro',
            'isbn' => '123456789',
            'data_publicacao' => '1899-01-01',
            'autor_id' => $autor->id,
        ]);

        $response->assertRedirect('/livros');

        $this->assertDatabaseHas('livros', [
            'titulo' => 'Dom Casmurro',
            'isbn' => '123456789',
            'autor_id' => $autor->id,
        ]);
    }

    public function test_deve_atualizar_livro(): void
    {
        $autor = Autor::create([
            'nome' => 'Autor Teste',
            'nacionalidade' => 'Brasileiro',
        ]);

        $this->post('/livros', [
            'titulo' => 'Livro Antigo',
            'isbn' => '111111',
            'data_publicacao' => '2000-01-01',
            'autor_id' => $autor->id,
        ]);

        $livro = Livro::where('titulo', 'Livro Antigo')->first();

        $response = $this->put('/livros/update/' . $livro->id, [
            'titulo' => 'Livro Atualizado',
            'isbn' => '222222',
            'data_publicacao' => '2001-01-01',
            'autor_id' => $autor->id,
        ]);

        $response->assertRedirect('/livros');

        $this->assertDatabaseHas('livros', [
            'id' => $livro->id,
            'titulo' => 'Livro Atualizado',
            'isbn' => '222222',
        ]);
    }

    public function test_deve_excluir_livro(): void
    {
        $autor = Autor::create([
            'nome' => 'Autor Teste',
            'nacionalidade' => 'Brasileiro',
        ]);

        $this->post('/livros', [
            'titulo' => 'Livro Excluir',
            'isbn' => '999999',
            'data_publicacao' => '2000-01-01',
            'autor_id' => $autor->id,
        ]);

        $livro = Livro::where('titulo', 'Livro Excluir')->first();

        $response = $this->delete('/livros/' . $livro->id);

        $response->assertRedirect('/livros');

        $this->assertDatabaseMissing('livros', [
            'id' => $livro->id,
        ]);
    }
}