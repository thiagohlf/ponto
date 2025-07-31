@echo off
echo ========================================
echo  Sistema de Ponto - Modo Desenvolvimento
echo ========================================
echo.

echo [1/4] Parando containers de produção...
docker-compose down

echo [2/4] Iniciando em modo desenvolvimento...
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build

echo [3/4] Aguardando serviços...
timeout /t 20 /nobreak >nul

echo [4/4] Configurando ambiente de desenvolvimento...
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

echo.
echo ========================================
echo  Desenvolvimento iniciado!
echo ========================================
echo.
echo Acesse: http://localhost
echo.
echo Recursos de desenvolvimento:
echo   - Hot reload ativado
echo   - Debug habilitado
echo   - Xdebug configurado
echo   - Logs detalhados
echo.
pause