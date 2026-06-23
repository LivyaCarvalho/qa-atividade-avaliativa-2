# Atividade Avaliativa 2 — Testes de Integração

## Sobre o projeto

Este repositório contém os testes de integração desenvolvidos para a **Atividade Avaliativa 2 da disciplina de Qualidade de Software**.

A aplicação foi desenvolvida em Laravel por outra turma. Nesta etapa, o objetivo foi atuar como equipe de testes, verificando a integração entre **rotas, controllers, models, views e banco de dados**, além de preparar a execução automática dos testes a cada Pull Request direcionado à branch `develop`.

## Objetivo da atividade

Os testes foram criados para verificar, sempre que aplicável:

* operações bem-sucedidas de CRUD;
* validações de dados obrigatórios ou inválidos;
* respostas HTTP e redirecionamentos;
* regras de negócio existentes;
* persistência correta dos dados;
* garantia de que dados inválidos não sejam persistidos;
* situações que possam causar regressões.

## Tecnologias utilizadas

* PHP 8.3 ou superior;
* Laravel 13;
* PHPUnit;
* SQLite em memória para os testes;
* Xdebug para cobertura de código;
* Docker e Docker Compose;
* Git e GitHub;
* GitHub Actions.

## Estrutura dos testes

Os testes implementados estão localizados em `tests/Feature`:

```text
tests/Feature/
├── AutorTest.php
├── BibliotecaPessoaTest.php
├── BibliotecaTest.php
├── LivroTest.php
├── PessoaTest.php
└── UserTest.php
```

O projeto também mantém os dois testes de exemplo que já existiam na estrutura inicial do Laravel.

## Cenários testados

### Autores

Foram implementados testes para:

* listar autores;
* cadastrar autor com dados válidos;
* rejeitar cadastro sem nome;
* rejeitar data de nascimento inválida;
* atualizar os dados de um autor;
* confirmar os dados persistidos na tabela `autores`.

### Livros

Foram implementados testes para:

* listar livros;
* cadastrar livro com dados válidos;
* relacionar o livro a um autor;
* atualizar os dados de um livro;
* excluir um livro;
* confirmar a criação, atualização e exclusão no banco de dados.

### Pessoas

Foram implementados testes para:

* listar pessoas;
* cadastrar pessoa com dados válidos;
* impedir cadastro quando a senha e a confirmação são diferentes;
* atualizar os dados de uma pessoa;
* impedir alteração de senha quando a confirmação é diferente;
* confirmar que uma pessoa com senhas diferentes não é persistida.

### Bibliotecas

Foram implementados testes para:

* listar bibliotecas;
* cadastrar biblioteca com dados válidos;
* atualizar os dados de uma biblioteca;
* excluir uma biblioteca;
* retornar resposta HTTP `404` ao tentar excluir uma biblioteca inexistente;
* confirmar a criação, atualização e exclusão no banco de dados.

### Usuários

Foram implementados testes para:

* listar usuários;
* cadastrar usuário com dados válidos;
* atualizar os dados e a função de um usuário;
* excluir usuário;
* redirecionar corretamente ao tentar excluir um usuário inexistente.

### Relacionamento entre Biblioteca e Pessoa

Foram implementados testes para:

* associar uma pessoa existente a uma biblioteca;
* impedir associação de pessoa inexistente;
* impedir que a mesma pessoa seja associada duas vezes à mesma biblioteca;
* confirmar a persistência do relacionamento na tabela `biblioteca_pessoa`.

## Atendimento aos critérios propostos

| Critério                        | Situação                                                                      |
| ------------------------------- | ----------------------------------------------------------------------------- |
| Cenários válidos de CRUD        | Atendido nos módulos em que as operações estão implementadas                  |
| Validações de entrada           | Atendido parcialmente, de acordo com as validações existentes nos controllers |
| Respostas HTTP                  | Testados status `200`, `404` e redirecionamentos `302`                        |
| Regras de negócio               | Testadas confirmação de senha, existência de pessoa e associação duplicada    |
| Dados inválidos não persistidos | Verificado com erros de sessão e `assertDatabaseMissing`                      |
| Prevenção de regressões         | Os principais fluxos e relacionamentos estão protegidos pelos testes          |

A aplicação utiliza rotas web, views e redirecionamentos. Por isso, respostas `302` são esperadas em operações de formulário e em falhas de validação. Códigos como `201` e `422` seriam mais comuns em uma API JSON.

## Resultado dos testes

Na última execução completa foram obtidos:

```text
29 testes aprovados
66 asserções
Nenhum teste falhando
```

Dos 29 testes executados:

* 27 foram desenvolvidos para esta atividade;
* 2 são testes de exemplo que já existiam no projeto.

## Cobertura de código

A cobertura foi calculada com Xdebug utilizando o comando:

```bash
XDEBUG_MODE=coverage php artisan test --coverage
```

Resultado obtido na última execução:

```text
Cobertura total: 67,3%
```

No ambiente Docker, a cobertura também pode ser executada com:

```bash
docker exec -it app_laravel bash
XDEBUG_MODE=coverage /usr/bin/php8.4 artisan test --coverage
```

## Problemas identificados durante os testes

### 1. Campo `data_nascimento` do Autor não é persistido

O campo `data_nascimento`:

* existe na migration da tabela `autores`;
* é validado pelo `AutorController`;
* é enviado nas operações de criação e atualização.

Entretanto, o campo não está presente no atributo `$fillable` do model `Autor`. Por isso, o valor enviado não é persistido quando o controller utiliza `Autor::create()` ou `$autor->update()`.

Além disso, o model possui o campo `sobrenome` no `$fillable`, mas esse campo não existe na migration da tabela `autores`.

### 2. Inconsistência entre o model e a tabela de Livros

A migration e o `LivroController` utilizam os campos:

```text
titulo
isbn
data_publicacao
autor_id
```

Entretanto, o `$fillable` do model `Livro` contém:

```text
titulo
autor
editora
ano_publicacao
isbn
```

Os campos `data_publicacao` e `autor_id` não estão no `$fillable`, enquanto `autor`, `editora` e `ano_publicacao` não correspondem à estrutura atual da tabela. Essa inconsistência fez com que a criação direta com `Livro::create()` não persistisse todos os dados obrigatórios.

### 3. Exclusão de Pessoa não implementada

A rota de exclusão de pessoas existe, mas o método abaixo está vazio no `PessoaController`:

```php
public function destroy($id)
{
}
```

Dessa forma, a exclusão de pessoas ainda não está funcional.

### 4. Rotas de Autor sem métodos correspondentes

As rotas de recurso criam endpoints de visualização e exclusão de autores, porém o `AutorController` não possui os métodos `show()` e `destroy()`.

Essas rotas podem gerar erro caso sejam acessadas.

### 5. Rota de visualização de Pessoa sem método correspondente

A rota de recurso de pessoas cria o endpoint `show`, mas o `PessoaController` não possui o método `show()`.

### 6. Validações insuficientes

Os controllers de Livro, Pessoa, Biblioteca e Usuário não possuem validações completas para todos os campos recebidos.

Entre os pontos que ainda poderiam ser validados estão:

* campos obrigatórios;
* formato de e-mail;
* unicidade de e-mail;
* existência de chaves estrangeiras;
* formato e tamanho dos dados;
* valores permitidos para funções de usuário.

A ausência dessas validações pode permitir dados inconsistentes ou fazer com que erros sejam tratados diretamente pelo banco de dados.

### 7. Nome de rota incorreto no tratamento de erro de Biblioteca

Em alguns blocos de tratamento de exceção, o `BibliotecasController` tenta redirecionar para:

```php
route('bibliotecas.new')
```

Entretanto, a rota existente para o formulário de criação é `bibliotecas.create`. Caso uma exceção ocorra, o próprio tratamento do erro pode gerar uma nova falha de rota inexistente.

## Testes que ainda estão falhando

Atualmente, não existem testes falhando. A última execução apresentou 29 testes aprovados e 66 asserções.

As funcionalidades incompletas ou inconsistentes identificadas foram registradas neste documento. Alguns cenários não foram transformados em testes obrigatoriamente falhos para que a suíte principal permanecesse estável e pudesse ser executada automaticamente no processo de integração contínua.

## Configuração do ambiente sem Docker

Copie o arquivo de configuração:

```bash
cp .env.example .env
```

Instale as dependências:

```bash
composer install
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

Para utilizar SQLite na aplicação local, configure no `.env`:

```env
DB_CONNECTION=sqlite
```

Crie o banco, execute as migrations e os seeders:

```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

Inicie a aplicação:

```bash
php artisan serve
```

## Configuração do ambiente com Docker

Construir a imagem:

```bash
docker compose build --no-cache
```

Iniciar o ambiente:

```bash
docker compose up
```

Parar o ambiente:

```bash
docker compose down
```

Acompanhar os logs:

```bash
docker compose logs -f app
```

Entrar no container:

```bash
docker exec -it app_laravel bash
```

Executar comandos Artisan pelo Docker:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate:rollback
docker compose exec app php artisan db:seed
```

## Execução dos testes

Executar todos os testes:

```bash
php artisan test
```

Executar somente um grupo de testes:

```bash
php artisan test --filter AutorTest
php artisan test --filter LivroTest
php artisan test --filter PessoaTest
php artisan test --filter BibliotecaTest
php artisan test --filter UserTest
php artisan test --filter BibliotecaPessoaTest
```

Os testes utilizam SQLite em memória, configurado no arquivo `phpunit.xml`, evitando alterações no banco utilizado pela aplicação.

## GitHub Actions

O workflow deve estar localizado em:

```text
.github/workflows/tests.yml
```

Ele deve ser executado automaticamente em Pull Requests direcionados à branch `develop`, realizando as seguintes etapas:

1. baixar o código do repositório;
2. configurar o PHP e as extensões necessárias;
3. instalar as dependências do Composer;
4. criar o arquivo `.env`;
5. gerar a chave da aplicação;
6. executar `php artisan test`.

> Antes da entrega, é necessário confirmar que o arquivo do workflow foi commitado e que a execução aparece com sucesso no Pull Request.

## Uso de Inteligência Artificial

Ferramentas de Inteligência Artificial foram utilizadas como apoio para:

* analisar a estrutura da aplicação;
* identificar cenários de teste relevantes;
* auxiliar na criação inicial dos testes;
* interpretar mensagens de erro;
* comparar controllers, models e migrations;
* revisar os resultados e a cobertura.

Os testes foram executados individualmente, analisados e ajustados de acordo com o comportamento real da aplicação.

## Fluxo de versionamento

As alterações foram desenvolvidas na branch:

```text
testes-integracao
```

O Pull Request final deve utilizar:

```text
base: develop
compare: testes-integracao
```

Os testes foram organizados em commits separados por módulo para facilitar o acompanhamento da evolução do trabalho.

## Conclusão

Os testes implementados validam os principais fluxos da aplicação e verificam a integração entre rotas, controllers, models, views e banco de dados.

Além de confirmar operações bem-sucedidas, a atividade permitiu identificar problemas reais na persistência de dados, inconsistências entre models e migrations, rotas sem implementação correspondente e ausência de validações.

A suíte atual possui 29 testes aprovados, 66 asserções e cobertura total de 67,3%, contribuindo para a detecção de regressões e para a melhoria da qualidade do sistema.


# Executar ambiente localhost

### Construir imagem docker
```
docker compose build --no-cache
```

### Iniciar todo o ambiente
```
docker compose up
```

### Parar todo o ambiente
```
docker compose down
```

### Ver logs em tempo real
```
docker compose logs -f app
```

### Executar command no artisan
```
docker compose exec app php artisan 
<comando>
```
Exemplos: 
```
docker compose exec app php artisan migrate

docker compose exec app php artisan migrate:rollback

docker compose exec app php artisan db:seed

docker compose exec app php artisan db:seed PessoaBibliotecaSeeder
```


### Cobertura de teste com xdebug
```
docker exec -it app_laravel bash
XDEBUG_MODE=coverage /usr/bin/php8.4 artisan test --coverage
```





<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
