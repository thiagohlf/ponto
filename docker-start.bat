@echo off
echo ========================================
echo  Sistema de Ponto Eletrônico - Docker
echo ========================================
echo.

echo [1/5] Verificando Docker...
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERRO: Docker não está instalado ou não está no PATH
    echo Por favor, instale o Docker Desktop primeiro
    pause
    exit /b 1
)

echo [2/5] Parando containers existentes...
docker-compose down

echo [3/5] Construindo imagens...
docker-compose build --no-cache

echo [4/5] Iniciando serviços...
docker-compose up -d

echo [5/5] Aguardando serviços ficarem prontos...
timeout /t 30 /nobreak >nul

echo.
echo ========================================
echo  Verificando status dos serviços...
echo ========================================
docker-compose ps

echo.
echo ========================================
echo  Executando configurações iniciais...
echo ========================================

echo Gerando chave da aplicação...
docker-compose exec app php artisan key:generate

echo Executando migrações...
docker-compose exec app php artisan migrate --force

echo Executando seeders...
docker-compose exec app php artisan db:seed --force

echo Limpando cache...
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

echo Otimizando para produção...
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo.
echo ========================================
echo  Sistema iniciado com sucesso!
echo ========================================
echo.
echo Acesse: http://localhost
echo.
echo Comandos úteis:
echo   docker-compose logs -f          - Ver logs em tempo real
echo   docker-compose exec app bash    - Acessar container da aplicação
echo   docker-compose down             - Parar todos os serviços
echo   docker-compose restart          - Reiniciar serviços
echo.
pause