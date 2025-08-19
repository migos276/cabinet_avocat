-- Connexion en tant qu'administrateur MySQL (root)
-- mysql -u root -p

-- 1. Créer la base de données
CREATE DATABASE cabinet_excellence CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Créer l'utilisateur
CREATE USER 'cabinet_user'@'localhost' IDENTIFIED BY '1migos2migos3migos4migos';

-- 3. Accorder tous les privilèges sur la base de données
GRANT ALL P
RIVILEGES ON cabinet_excellence.* TO 'cabinet_user'@'localhost';

-- 4. Actualiser les privilèges
FLUSH PRIVILEGES;

-- 5. Vérifier la création (optionnel)
SELECT User, Host FROM mysql.user WHERE User = 'cabinet_user';
SHOW GRANTS FOR 'cabinet_user'@'localhost';