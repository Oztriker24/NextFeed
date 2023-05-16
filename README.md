# Introduction
Socle permettant de commencer rapidement un projet sous Symfony.

# Installation
### Copy and configure the Docker environment file (`docker/.env`) :

```bash
cp docker/.env.example docker/.env
```

```dotenv
# Docker project name
COMPOSE_PROJECT_NAME=my-project

# Forwarded web port on your host
DOCKER_APP_PORT=8000

# Database
DATABASE=db_laravel
PASSWORD_ROOT=notSecureChangeMe
USERNAME=root
PASSWORD=notSecureChangeMe
```

### Copy and configure the Symfony environment file (`symfony/.env`) :

```bash
cp symfony/.env.example symfony/.env
```

```dotenv
APP_NAME="My Project"
APP_ENV=local
DATABASE_URL='mysql://root:notSecureChangeMe@database/db_symfony'
```

### Setup variables for database configuration (`build/init.sql`) :
Use the same values than in the .env file
```mysql
SET @dbname = 'db_symfony';
SET @username = 'test';
SET @password = 'notSecureChangeMe';
```

### Log in to the registry with Azure DevOps credentials

```bash
az acr login --name testdockerzzitne
```
ou
```bash
docker login testdockerzzitne.azurecr.io
```

### Run the `docker-compose` command (`docker/`)

```bash
docker-compose -p shared -f docker-compose.shared.yml up -d
docker-compose up -d
```

### To open a shell in the app:

```bash
docker exec -it symfony bash
```  
