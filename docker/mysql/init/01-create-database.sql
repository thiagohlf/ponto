-- Script de inicialização do banco de dados
-- Criado automaticamente pelo Docker

-- Criar database se não existir
CREATE DATABASE IF NOT EXISTS ponto_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar usuário se não existir
CREATE USER IF NOT EXISTS 'ponto_user'@'%' IDENTIFIED BY 'ponto_password_secure_2024';

-- Conceder privilégios
GRANT ALL PRIVILEGES ON ponto_db.* TO 'ponto_user'@'%';

-- Aplicar mudanças
FLUSH PRIVILEGES;

-- Configurações específicas para o sistema de ponto
USE ponto_db;

-- Configurar timezone
SET time_zone = '-03:00';

-- Configurações de performance para o sistema
SET GLOBAL innodb_buffer_pool_size = 1073741824; -- 1GB
SET GLOBAL query_cache_size = 134217728; -- 128MB
SET GLOBAL max_connections = 200;