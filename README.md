# DoaPet - PoC

DoaPet é uma Prova de Conceito (PoC) de uma plataforma para conectar doadores de animais a possíveis adotantes.

## Missão

Facilitar o processo de adoção de animais, fornecendo um espaço onde usuários podem anunciar animais para adoção e interessados podem encontrar um novo companheiro.

## Tecnologias Utilizadas

*   **Backend:** PHP >= 7.4 (Puro, sem frameworks)
*   **Frontend:** HTML5, CSS3, JavaScript (ES6+)
*   **Estilização:** Bootstrap 5 (via CDN)
*   **Banco de Dados:** MySQL 8
*   **Servidor:** Apache (via Docker)
*   **Gerenciador de Dependências PHP:** Composer (para autoload PSR-4)
*   **Containerização:** Docker, Docker Compose

## Fluxo Mínimo da PoC

1.  **Cadastro de Usuário:** Novos usuários podem criar uma conta.
2.  **Login de Usuário:** Usuários cadastrados podem acessar suas contas.
3.  **Publicação de Anúncio:** Usuários logados podem criar anúncios para animais que desejam doar.
4.  **Listagem de Anúncios:** Todos os anúncios são exibidos em uma página pública.
5.  **Edição e Exclusão de Anúncios:** Usuários logados podem editar ou excluir seus próprios anúncios.

## Pré-requisitos

*   [Docker](https://www.docker.com/get-started) instalado.
*   [Docker Compose](https://docs.docker.com/compose/install/) instalado.

## Como Executar o Projeto

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/seu-usuario/doapet-poc.git
    cd doapet-poc
    ```

2.  **Crie o arquivo de ambiente:**
    Copie o arquivo `.env.example` para `.env` e, se necessário, ajuste as variáveis de ambiente.
    ```bash
    cp .env.example .env
    ```
    As credenciais padrão para o banco de dados no `.env.example` são:
    ```
    DB_HOST=db
    DB_NAME=doapet
    DB_USER=root
    DB_PASS=secret
    ```

3.  **Instale as dependências do Composer (para autoload):**
    Se você tiver o Composer instalado localmente:
    ```bash
    composer install
    ```
    Alternativamente, você pode rodar o Composer dentro de um container Docker:
    ```bash
    docker run --rm --interactive --tty \
      --volume $PWD:/app \
      composer install --ignore-platform-reqs
    ```

4.  **Suba os containers Docker:**
    Na raiz do projeto, execute:
    ```bash
    docker compose up -d
    ```
    Este comando irá construir as imagens (se ainda não existirem) e iniciar os serviços de `web` (PHP/Apache), `db` (MySQL) e `phpmyadmin`.

5.  **Acesse a aplicação:**
    Abra seu navegador e acesse: [http://localhost:8000](http://localhost:8000)

6.  **Acesse o phpMyAdmin (opcional):**
    Para inspecionar o banco de dados, acesse: [http://localhost:8081](http://localhost:8081)
    *   Servidor: `db`
    *   Usuário: `root`
    *   Senha: `secret` (a mesma definida em `MYSQL_ROOT_PASSWORD` no `docker-compose.yml` e `DB_PASS` no `.env`)

## Estrutura de Pastas

```
.
├── database/
│   └── schema.sql        # Definições DDL e seeds iniciais
├── public/
│   ├── css/              # (Vazio, Bootstrap via CDN)
│   ├── js/
│   │   └── script.js     # Scripts JavaScript front-end
│   ├── uploads/          # Diretório para fotos de anúncios (criado dinamicamente se necessário)
│   └── index.php         # Ponto de entrada e roteador principal
├── src/
│   ├── Controllers/
│   │   ├── AdController.php
│   │   └── UserController.php
│   ├── Models/
│   │   ├── Ad.php
│   │   └── User.php
│   └── Database.php      # Classe de conexão com o banco (PDO Singleton)
├── views/
│   ├── ad-list.php
│   ├── create-ad.php
│   ├── edit-ad.php
│   ├── footer.php
│   ├── header.php
│   ├── login.php
│   └── register.php
├── .env.example          # Arquivo de exemplo para variáveis de ambiente
├── .gitignore
├── composer.json         # Configuração do Composer (autoload)
├── docker-compose.yml    # Configuração dos serviços Docker
└── README.md             # Este arquivo
```

## Padrões de Código

*   **PHP:** PSR-12
*   **Frontend:** HTML5 semântico, CSS3 (via Bootstrap 5), JavaScript moderno.

## Observações

*   Este é um projeto de Prova de Conceito e pode não incluir todas as funcionalidades de uma aplicação completa (ex: recuperação de senha, painel administrativo avançado, etc.).
*   As senhas são armazenadas usando `password_hash()` e verificadas com `password_verify()`.
*   As entradas do usuário são sanitizadas usando `filter_input()` com `FILTER_SANITIZE_SPECIAL_CHARS`.
