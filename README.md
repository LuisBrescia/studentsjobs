# 📦 Server

Esta pasta contém o backend do projeto, desenvolvido com [Laravel](https://laravel.com). Para mais informações, consulte a [documentação oficial do Laravel](https://laravel.com/docs).

<br>

## 🚀 Rodando Localmente

Neste tópico será mostrado como rodar o projeto localmente, utilizando o Docker e o Laravel Sail, junto com o Windows Subsystem for Linux (WSL), em necessidade de instalar o PHP, Composer ou banco de dados na máquina local. Caso não quer utilizar o WSL, você pode seguir as [instruções específicas para Windows](#💻-instruções-para-windows).

> Também é possível utilizar o [Laravel Valet](https://laravel.com/docs/valet), [Laravel Homestead](https://laravel.com/docs/homestead), ou até mesmo criar seu próprio ambiente para rodar o projeto.

### ✅ Pré-requisitos

-   [Docker](https://www.docker.com/products/docker-desktop)
-   [Windows Subsystem for Linux (WSL)](https://docs.microsoft.com/pt-br/windows/wsl/install)
-   [Windows Pro, Enterprise or Home](https://docs.microsoft.com/pt-br/windows/wsl/install#installing-wsl-on-windows-10)

### 🔧 Instalação de Dependências

Execute o comando abaixo para instalar as dependências com o Composer (utilizando o container oficial do Laravel Sail):

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

Em seguida, copie o arquivo de exemplo de ambiente:

```bash
cp .env.example .env
```

<br>

## ⚙️ Ambiente de desenvolvimento

Com o ambiente configurado, você pode iniciar o Sail e rodar o projeto.

### 🧩 Executando Comandos com Sail

Você pode executar **qualquer comando do Laravel** com o prefixo `sail`.

Por exemplo, ao invés de:

```bash
php artisan migrate
```

Use:

```bash
sail artisan migrate
```

Isso vale para qualquer comando do Laravel, como `tinker`, `migrate`, `db:seed`, entre outros.

<br>

### ▶️ Iniciar o servidor

```bash
sail up -d
```

> A flag `-d` executa o Sail em segundo plano.

### ⏹️ Parar o servidor

```bash
sail down
```

> Adicione a flag `-v` para remover os volumes persistentes criados.

### 🏷️ Alterar nome dos containers

Você pode alterar o nome dos artefatos gerados pelo Docker Compose com a variável `COMPOSE_PROJECT_NAME`:

```bash
COMPOSE_PROJECT_NAME=meu_projeto sail up -d
```

> **Atenção**: Esse comando deve ser utilizado **antes** de o Sail estar rodando.

<br>

## 🗃️ Conexão com Banco de Dados

### 📥 Criar as tabelas

```bash
sail artisan migrate
```

### 🌱 Popular com dados de exemplo

```bash
sail artisan db:seed
```

### 🔄 Resetar o banco de dados

```bash
sail artisan migrate:refresh --seed
```

### 🔐 Credenciais de Acesso (Padrão)

-   **Host**: `localhost`
-   **Porta**: `5432`
-   **Database**: `studentsjobs`
-   **Usuário**: `luisb`
-   **Senha**: `luisb`
-   **Autenticação**: Database Native

### 🌿 MongoDB

Para acessar a tabela `curriculos` do MongoDB, utilize os seguinte comando:

```bash
sail exec mongodb mongosh
show dbs
use admin
db.auth("luisb", "luisb")
use studentsjobs_mongodb
show collections
db.curriculos.find()
```

> Esses dados podem variar conforme sua configuração no `.env`.

<br>

## 💻 Instruções para Windows

Caso esteja utilizando Windows com Docker Desktop (necessário ser feito pelo PowerShell):

### 🧩 Instalação de dependências

```bash
docker run --rm -v C:/code/pmg-es-2025-1-ti5-myjobs/code/server:/var/www/html -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs
```

### ⚙️ Usando o Sail no PowerShell

Escreva antes `bash vendor/bin/sail` para utilizar os comandos do Sail.

> Para facilitar, você pode criar um **alias permanente** no seu perfil do PowerShell:

#### Passo a passo:

1. Verifique se o seu perfil existe:

```bash
Test-Path $PROFILE
```

-   Se retornar `False`, crie o perfil com:

```bash
New-Item -Type File -Path $PROFILE -Force
```

2. Abra o perfil no editor:

```bash
notepad $PROFILE
```

> (Ou use o VS Code: `code $PROFILE`)

3. Adicione a função no final do arquivo:

```bash
function sail() {
    if [ -f ./vendor/bin/sail ]; then
        bash ./vendor/bin/sail "$@"
    else
        echo "Sail não encontrado no diretório atual."
    fi
}
```

4. Salve o arquivo, feche e **reinicie o PowerShell**.

> Agora você poderá usar `sail` diretamente no terminal, sem precisar do `bash vendor/bin/sail`.

### ‼️ Atenção

-   Setar o campo `Accept` para `application/json` no seu HTTP Client.

## P.S. Lembre-se de rodar:

```bash
sail artisan install:api
sail composer install
```
