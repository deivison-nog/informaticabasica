# Informática Básica — Plataforma de Ensino

Sistema educacional simples em **PHP + Bootstrap + JavaScript** para aulas de informática básica com login por CPF e senha e três níveis de acesso.

## Funcionalidades

| Perfil | Capacidades |
|---|---|
| **Administrador** | Adicionar/remover alunos e professores |
| **Professor** | Adicionar alunos, ver planos de aula, publicar/despublicar conteúdo e questionários |
| **Aluno** | Acessar aulas e questionários liberados pelo professor |

## Conteúdo das Aulas

- **Aula 1** — Conceitos básicos de informática (hardware, software, SO, periféricos)
- **Aula 2** — Internet, e-mail e arquivos
- **Aula 3** — Pacote Office, nuvem e redes
- **Aula 4** — Segurança digital e tendências de 2026

Cada aula contém espaços para imagens claramente identificados como `[IMAGEM X.Y]`.

## Instalação

**Requisitos:** PHP 8.0+ com extensão `json`, servidor web (Apache/Nginx) ou `php -S`.

```bash
# Iniciar servidor de desenvolvimento
cd informaticabasica
php -S localhost:8000
# Acesse http://localhost:8000
```

Não é necessário banco de dados — os dados são armazenados em arquivos JSON na pasta `data/`.

## Credenciais de Teste

| Perfil | CPF | Senha |
|---|---|---|
| Administrador | `000.000.000-00` | `admin123` |
| Professor | `111.111.111-11` | `prof123` |
| Aluno | `222.222.222-22` | `aluno123` |

## Estrutura de Arquivos

```
informaticabasica/
├── index.php              # Página de login
├── logout.php
├── config/
│   └── functions.php      # Funções auxiliares (usuários, publicações)
├── data/
│   ├── users.json         # Usuários cadastrados
│   └── publicacoes.json   # Status de publicação das aulas e questionários
├── includes/
│   ├── auth.php           # Controle de autenticação e permissões
│   ├── header.php         # Cabeçalho HTML + navbar + sidebar
│   └── footer.php         # Rodapé HTML
├── admin/
│   ├── index.php          # Dashboard do administrador
│   ├── usuarios.php       # Listar/remover usuários
│   └── add_user.php       # Formulário de cadastro
├── professor/
│   ├── index.php          # Dashboard do professor
│   ├── aulas.php          # Planos de aula
│   ├── publicar.php       # Publicar/despublicar aulas e questionários
│   └── add_aluno.php      # Adicionar aluno
├── aluno/
│   ├── index.php          # Dashboard do aluno
│   ├── aula.php           # Ver aulas disponíveis
│   └── questionario.php   # Fazer questionários
└── content/
    ├── aula1.php          # Conteúdo: Conceitos básicos
    ├── aula2.php          # Conteúdo: Internet, e-mail e arquivos
    ├── aula3.php          # Conteúdo: Pacote Office, nuvem e redes
    ├── aula4.php          # Conteúdo: Segurança digital e tendências 2026
    ├── aulas_conteudo.php # Visualização consolidada de todo o material
    └── questionarios_data.php  # Dados dos 4 questionários (7 questões cada)
```