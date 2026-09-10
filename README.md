# Brandify

Encurtador de links para apresentações do [Gamma](https://gamma.app) com sua própria marca.

Cole o snippet `<iframe>` que o Gamma gera para um deck, defina um slug e (opcionalmente) um diretório para organizar, e o Brandify gera uma URL curta e com o seu domínio:

```
seudominio.com/r/{diretorio}/{slug}
```

Ao acessar essa URL, a apresentação é exibida embutida numa página do seu domínio — em vez do link padrão do Gamma. Cada proposta pode ter uma data de expiração opcional; depois dela, o link retorna 404.

## Funcionalidades

- **Painel administrativo** (Filament) para criar e organizar propostas em diretórios.
- **Parser de embed**: cola o HTML do `<iframe>` do Gamma e o Brandify extrai a URL e o título automaticamente.
- **Slugs únicos e amigáveis**, sugeridos a partir do título da apresentação.
- **Expiração de links** — defina até quando um link fica ativo.
- **Backup por email**: pensado para hospedagem compartilhada, sem depender de queue worker. Um comando artisan, disparado pelo cron do painel do host, envia um CSV de diretórios e propostas por email (via [Resend](https://resend.com)). Há também um importador para restaurar a partir desses arquivos.

## Stack

- [Laravel 13](https://laravel.com)
- [Filament 4](https://filamentphp.com)
- PostgreSQL
- [Resend](https://resend.com) (envio de email)

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure no `.env`:

- Conexão com o banco (`DB_*`).
- `ADMIN_EMAIL` e `ADMIN_PASSWORD` — se preenchidos, o seeder cria um usuário admin automaticamente. Sem eles, use `php artisan make:filament-user` para criar manualmente.
- (Opcional) `MAIL_MAILER=resend` e `RESEND_API_KEY` para habilitar o backup por email.

```bash
php artisan migrate --seed
php artisan serve
```

Acesse o painel em `/admin`.

### Backup automático via email

O Brandify pode enviar um backup em CSV (diretórios e propostas) por email usando o Resend. O backup pode ser disparado de duas formas:

- **Manual**: botão "Enviar backup agora" na tela de Configurações do painel.
- **Agendado**: comando artisan `backup:send`, pensado para ser chamado direto pelo cron do painel de hosting (não depende do Laravel Scheduler nem de um queue worker).

Configuração:

1. Defina `MAIL_MAILER=resend` e `RESEND_API_KEY=<sua chave>` no `.env`.
2. Configure `MAIL_FROM_ADDRESS` com um endereço de um domínio verificado na sua conta Resend.
3. Na tela de Configurações do painel, preencha o campo **Email de backup** — é para onde o backup será enviado.

Agendando o envio no hosting compartilhado, aponte o cron job do painel do host diretamente para o comando artisan:

```bash
php /caminho/para/o/projeto/artisan backup:send >> /caminho/para/o/projeto/storage/logs/backup-cron.log 2>&1
```

Ajuste a frequência (diária, semanal etc.) direto no painel de cron do hosting (cPanel, Hostinger, etc). O comando é síncrono e envia o email na hora.

Na mesma tela de Configurações, o botão "Importar backup" permite enviar os arquivos `directories.csv` e `proposals.csv` (gerados por um backup anterior) para **substituir por completo** os dados atuais. Essa ação não pode ser desfeita.

### Rodando migrations em produção (sem SSH)

Como o hosting compartilhado não dá acesso a terminal, migrations não podem ser executadas diretamente após um deploy. Em vez de expor uma rota HTTP para isso, o Brandify usa um gatilho por arquivo, verificado por um cron que roda a cada minuto:

```bash
* * * * * php /caminho/para/o/projeto/artisan deploy:migrate-if-triggered >> /caminho/para/o/projeto/storage/logs/migrate-cron.log 2>&1
```

O comando não faz nada a menos que exista o arquivo `storage/app/migrate.trigger`. Para rodar as migrations pendentes:

1. Crie um arquivo vazio em `storage/app/migrate.trigger` (pelo gerenciador de arquivos do painel, ou por FTP).
2. No próximo minuto, o cron encontra o arquivo, roda `php artisan migrate --force` e remove o gatilho.
3. Acompanhe o resultado em `storage/logs/migrate-cron.log`.

O comando usa um lock (`storage/app/migrate.lock`) para não rodar duas vezes em paralelo caso uma execução anterior ainda esteja em andamento.

## Ajuste de permissões em hosting compartilhado

O script `bin/fix-permissions.sh` ajusta as permissões de diretórios e arquivos após o deploy em hosting compartilhado. Copie `.env-security.example` para `.env-security` e ajuste os valores conforme o seu provedor.

## Licença

[MIT](LICENSE).
