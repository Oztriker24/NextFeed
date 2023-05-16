
######## VARIABLES ##########
SET @dbname = 'feed';
SET @username = 'feed'; # Other than root
SET @password = 'notSecureChangeMe';
######## VARIABLES ##########


######## DO NOT EDIT THIS LINES BELOW (unless you know what you're doing :) ##########
SET @createDatabaseQuery = CONCAT('CREATE DATABASE IF NOT EXISTS`', @dbname, '`');
PREPARE createDatabase FROM @createDatabaseQuery;
EXECUTE createDatabase;
DEALLOCATE PREPARE createDatabase;

SET @createUserQuery = CONCAT('CREATE USER "',@username,'"@"%" IDENTIFIED BY "',@password,'" ');
PREPARE createUser FROM @createUserQuery;
EXECUTE createUser;
DEALLOCATE PREPARE createUser;

SET @setGrantQuery = CONCAT('GRANT ALL PRIVILEGES ON ', @dbname, '.* TO "', @username, '"@"%"');
PREPARE setGrant FROM @setGrantQuery;
EXECUTE setGrant;
DEALLOCATE PREPARE setGrant;
