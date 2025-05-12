# PI2 – Prova de Conceito (Grupo 48)

**Fluxo implementado:** Cadastro de usuário e publicação de anúncio de doação.

## Estrutura do Projeto

- `public/`  
  - `index.php` – ponto de entrada  
  - `css/`, `js/`, `uploads/`  

- `src/`  
  - `Database.php` – conexão com o banco  
  - `UserController.php`  
  - `AdController.php`  

- `views/`  
  - `header.php`, `footer.php`  
  - `register.php`, `create-ad.php`, `ad-list.php`  

- `database/`  
  - `schema.sql`  

- `tests/` (para testes futuros)

## Como rodar localmente

1. Instalar PHP ≥ 7.4, Composer e MySQL/SQLite.  
2. Clonar o repositório:
   ```bash
   git clone git@github.com:SEU_USUARIO/PI2_Grupo48.git
   cd PI2_Grupo48
   ```
3. Instalar dependências (se houver):
   ```bash
   composer install
   ```
4. Importar o esquema:
   ```bash
   mysql -u root -p seu_banco < database/schema.sql
   ```
5. Iniciar servidor PHP:
   ```bash
   php -S localhost:8000 -t public
   ```
6. Acessar http://localhost:8000

## Próximos passos

- Criar `schema.sql` em `database/`.
- Implementar `Database.php` em `src/`.
- Desenvolver as primeiras `views` em `views/`.
