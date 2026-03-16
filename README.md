# 🎮 PokeAPI App — Luiz Felipe

Uma aplicação web construída com **Laravel** que consome a [PokéAPI](https://pokeapi.co/) para exibir e gerenciar informações de Pokémon, com autenticação de usuários e interface responsiva com Tailwind CSS.

---

## Tecnologias

- **PHP** com **Laravel** (framework principal)
- **Tailwind CSS** (estilização)
- **Vite** (bundler de assets)
- **Docker** (containerização)
- **MySQL** (banco de dados, via Docker)

---

## Estrutura do Projeto

app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── AuthenticatedSessionController.php
│   │   └── Poke/
│   │       ├── PokemonController.php
│   │       └── Controller.php
│   └── Requests/
│       ├── Auth/
│       │   └── LoginRequest.php
│       └── Poke/
│           ├── SearchPokemonRequest.php
│           └── ShowPokemonRequest.php
├── Models/
│   ├── Poke/
│   │   ├── Pokemon.php
│   │   └── Type.php
│   └── User.php
├── Policies/Poke/
│   └── PokemonPolicy.php
├── Providers/
│   └── AppServiceProvider.php
├── Repositories/Poke/
│   └── PokemonRepository.php
└── Services/
    ├── PokeApiClient.php
    └── PokemonImporter.php

resources/
└── views/
    ├── auth/
    │   └── login.blade.php
    ├── components/
    ├── layouts/
    ├── pokemon/
    │   ├── index.blade.php
    │   └── show.blade.php
    └── users/
        ├── show.blade.php
        └── users.blade.php

---

## Requisitos

- PHP >= 8.1
- Composer
- Node.js >= 18 e npm
- Docker e Docker Compose *(opcional, mas recomendado)*

---

## Instalação

### Com Docker (recomendado)

```bash
# Clone o repositório
git clone https://github.com/Luiz-Felip-DEV/pokeapi-app-luiz_felipe.git

# Copie o arquivo de variáveis de ambiente
cp .env.example .env

# Suba os containers
docker compose up -d --build

# Instale as dependências PHP dentro do container
docker compose exec app composer install

# Gere a chave da aplicação
docker compose exec app php artisan key:generate

# Execute as migrations
docker compose exec app php artisan migrate

# Execute as seeds
docker compose exec app php artisan db:seed

# Instale as dependências JS e compile os assets
docker compose exec app npm install && npm run build
```

### Sem Docker

```bash
# Clone o repositório
git clone https://github.com/Luiz-Felip-DEV/pokeapi-app-luiz_felipe.git

# Copie o arquivo de variáveis de ambiente
cp .env.example .env

# Configure o banco de dados no arquivo .env

# Instale as dependências PHP
composer install

# Gere a chave da aplicação
php artisan key:generate

# Execute as migrations
php artisan migrate

# Execute as seeds
php artisan db:seed

# Instale as dependências JS e compile os assets
npm install && npm run dev

# Inicie o servidor de desenvolvimento
php artisan serve
```

A aplicação estará disponível em `http://localhost:8000/login`.

---

### Acessar a aplicação

1. Acesse `http://localhost:8000/login`
2. Faça login com suas credenciais
3. Navegue pela listagem de Pokémons
4. Use a busca para filtrar por nome ou tipo

---

## Funcionalidades

- ✅ **Autenticação** — Login e sessão de usuários
- ✅ **Listagem de Pokémons** — Exibe todos os Pokémon importados
- ✅ **Busca** — Pesquisa por nome ou tipo de Pokémon
- ✅ **Detalhes do Pokémon** — Página individual com informações completas
- ✅ **Integração com PokéAPI** — Importação via `PokeApiClient` e `PokemonImporter`
- ✅ **Policies** — Controle de acesso baseado em políticas do Laravel
- ✅ **Repository Pattern** — Abstração da camada de dados
- ✅ **Gerenciamento de Usuários** — Visualização de perfis

---

## Docker

O projeto inclui `Dockerfile` e `docker-compose.yml` prontos para uso.

```bash
# Subir os containers
docker-compose up -d

# Parar os containers
docker-compose down

# Ver logs
docker-compose logs -f app
```

## Rotas

GET / — Redireciona o usuário autenticado direto para a listagem de Pokémons.

GET /pokemons — Lista todos os Pokémons que já foram importados para o banco de dados.

GET /pokemons/favorites — Lista apenas os Pokémons marcados como favoritos pelo usuário.

GET /pokemons/{name} — Exibe a página de detalhes de um Pokémon específico, buscando pelo nome.

POST /pokemons/{name}/import — Importa um Pokémon da PokéAPI e salva no banco de dados local.

POST /pokemons/{name}/favorite — Adiciona um Pokémon à lista de favoritos do usuário.

DELETE /pokemons/{name}/favorite — Remove um Pokémon da lista de favoritos do usuário.

DELETE /pokemons/{name}/imported — Remove um Pokémon importado do banco de dados local.

GET /users — Lista todos os usuários cadastrados na aplicação.

GET /users/{id} — Exibe o perfil de um usuário específico pelo ID.

PUT /users/{id}/role — Atualiza o papel (role) de um usuário, como promover a administrador.

## Url Publica do Projeto

https://pokeapi-app-luiz-felipe-master-8qonod.laravel.cloud/login

> Desenvolvido por **Luiz Felipe**