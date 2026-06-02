# Laravel API — Pessoal

> **🚧 Projeto em desenvolvimento** — funcionalidades estão sendo adicionadas progressivamente.

API RESTful construída com Laravel 13, seguindo o padrão Service-Repository com injeção de dependência. O projeto tem como foco autenticação segura, controle de acesso baseado em grupos e gerenciamento de notas.

> Desenvolvido com auxílio de **pair programming** junto ao [Claude Code](https://claude.ai/code) — IA da Anthropic utilizada como parceiro de desenvolvimento durante todo o processo.

**Front-end do projeto:** [Allankl2/Front-end-daily](https://github.com/Allankl2/Front-end-daily)

---

## Tecnologias

| Tecnologia | Versão | Finalidade |
|---|---|---|
| **PHP** | ^8.3 | Linguagem principal |
| **Laravel** | ^13.8 | Framework |
| **Laravel Sanctum** | ^4.3 | Autenticação via token (API) |
| **SQLite** | — | Banco de dados (desenvolvimento) |
| **L5-Swagger** | ^11.0 | Documentação OpenAPI/Swagger |
| **PHPUnit** | ^12.5 | Testes |
| **Laravel Pint** | ^1.27 | Padronização de código |

---

## Arquitetura

O projeto utiliza o padrão **Service-Repository** com **Injeção de Dependência**, separando as responsabilidades em camadas:

```
Controller  →  Service  →  Repository  →  Model  →  Banco de dados
```

- **Controllers** — recebem a requisição, validam os dados e delegam para o Service
- **Services** — contêm toda a regra de negócio
- **Repositories** — abstraem o acesso ao banco de dados
- **Models** — representam as entidades do sistema

```
app/
├── Http/Controllers/     # Camada de apresentação
├── Services/             # Regras de negócio
├── Repositories/         # Acesso a dados
│   └── Contracts/        # Interfaces dos repositórios
├── Models/               # Entidades
└── Mail/                 # Mailables
```

---

## Banco de Dados

Banco utilizado em desenvolvimento: **SQLite**.

### Tabelas

| Tabela | Descrição |
|---|---|
| `users` | Usuários do sistema |
| `otp_codes` | Códigos OTP para verificação de email |
| `personal_access_tokens` | Tokens de autenticação (Sanctum) |
| `groups` | Grupos de acesso |
| `screens` | Telas/módulos do sistema |
| `endpoints` | Endpoints da API |
| `group_user` | Relação usuário ↔ grupo |
| `group_screen` | Relação grupo ↔ tela |
| `screen_endpoint` | Relação tela ↔ endpoint |
| `access_logs` | Auditoria de acessos |
| `notes` | Notas dos usuários |
| `sessions` | Sessões ativas |
| `cache` | Cache da aplicação |
| `jobs` | Fila de jobs |

---

## O que foi implementado

### Autenticação com verificação OTP
- Registro cria o usuário e envia um código de 6 dígitos para o email
- O código expira em **10 minutos**
- Máximo de **4 tentativas** erradas — após isso o OTP é bloqueado e o usuário deve se registrar novamente
- Após validação do OTP, o token de acesso é gerado e retornado

### Controle de acesso baseado em grupos (RBAC)
- Usuários pertencem a **grupos**
- Grupos têm acesso a **telas**
- Telas contêm **endpoints** liberados
- Auditoria de acesso registrada em `access_logs`

### Notas
- Criação e listagem de notas por usuário autenticado
- Suporte a tags (armazenadas como array JSON)

---

## Endpoints disponíveis

### Autenticação
| Método | Rota | Autenticação | Descrição |
|---|---|---|---|
| `POST` | `/auth/register` | Não | Registra usuário e envia OTP |
| `POST` | `/auth/verify-otp` | Não | Valida OTP e retorna token |
| `POST` | `/auth/login` | Não | Login com email e senha |
| `POST` | `/auth/logout` | Sim | Encerra a sessão |
| `GET` | `/auth/can-access` | Sim | Verifica autenticação |

### Usuários
| Método | Rota | Autenticação | Descrição |
|---|---|---|---|
| `GET` | `/users` | Sim | Lista todos os usuários |

### Notas
| Método | Rota | Autenticação | Descrição |
|---|---|---|---|
| `GET` | `/notes` | Sim | Lista notas do usuário |
| `POST` | `/notes` | Sim | Cria uma nova nota |

---

## Como rodar localmente

**Requisitos:** PHP 8.3+, Composer

```bash
# Clonar o repositório
git clone <url-do-repositorio>
cd Laravel-Meu-pessoal

# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Rodar as migrations
php artisan migrate

# Iniciar o servidor
php artisan serve
```

> Configure as variáveis de email no `.env` para o envio de OTP funcionar (`MAIL_MAILER`, `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD`).

---

## Status do projeto

- [x] Autenticação com token (Sanctum)
- [x] Verificação de email via OTP
- [x] Estrutura de grupos e controle de acesso (RBAC)
- [x] Auditoria de acessos
- [x] Notas por usuário
- [ ] Documentação Swagger completa
- [ ] Testes automatizados
- [ ] Configuração para produção (MySQL)
