@echo off
echo ========================================
echo  Verificação da Configuração Docker
echo ========================================
echo.

set "error_count=0"

echo [1/8] Verificando Docker...
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker não encontrado
    set /a error_count+=1
) else (
    echo ✅ Docker instalado
)

echo [2/8] Verificando Docker Compose...
docker-compose --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker Compose não encontrado
    set /a error_count+=1
) else (
    echo ✅ Docker Compose instalado
)

echo [3/8] Verificando arquivo docker-compose.yml...
if exist "docker-compose.yml" (
    echo ✅ docker-compose.yml encontrado
) else (
    echo ❌ docker-compose.yml não encontrado
    set /a error_count+=1
)

echo [4/8] Verificando Dockerfile...
if exist "Dockerfile" (
    echo ✅ Dockerfile encontrado
) else (
    echo ❌ Dockerfile não encontrado
    set /a error_count+=1
)

echo [5/8] Verificando configurações PHP...
if exist "docker\php\Dockerfile" (
    echo ✅ docker/php/Dockerfile encontrado
) else (
    echo ❌ docker/php/Dockerfile não encontrado
    set /a error_count+=1
)

if exist "docker\php\local.ini" (
    echo ✅ docker/php/local.ini encontrado
) else (
    echo ❌ docker/php/local.ini não encontrado
    set /a error_count+=1
)

echo [6/8] Verificando configurações Nginx...
if exist "docker\nginx\nginx.conf" (
    echo ✅ docker/nginx/nginx.conf encontrado
) else (
    echo ❌ docker/nginx/nginx.conf não encontrado
    set /a error_count+=1
)

if exist "docker\nginx\default.conf" (
    echo ✅ docker/nginx/default.conf encontrado
) else (
    echo ❌ docker/nginx/default.conf não encontrado
    set /a error_count+=1
)

echo [7/8] Verificando configurações MySQL...
if exist "docker\mysql\my.cnf" (
    echo ✅ docker/mysql/my.cnf encontrado
) else (
    echo ❌ docker/mysql/my.cnf não encontrado
    set /a error_count+=1
)

echo [8/8] Verificando configurações Redis...
if exist "docker\redis\redis.conf" (
    echo ✅ docker/redis/redis.conf encontrado
) else (
    echo ❌ docker/redis/redis.conf não encontrado
    set /a error_count+=1
)

echo.
echo ========================================
echo  Verificando arquivo .env...
echo ========================================
if exist ".env" (
    echo ✅ Arquivo .env encontrado
) else (
    echo ⚠️  Arquivo .env não encontrado - será criado automaticamente
)

echo.
echo ========================================
echo  Resumo da Verificação
echo ========================================
if %error_count% equ 0 (
    echo ✅ Todas as configurações estão corretas!
    echo ✅ O Docker está pronto para ser executado
    echo.
    echo Para iniciar o sistema, execute:
    echo   docker-start.bat
) else (
    echo ❌ Encontrados %error_count% problemas
    echo ❌ Corrija os problemas antes de executar o Docker
)

echo.
pause