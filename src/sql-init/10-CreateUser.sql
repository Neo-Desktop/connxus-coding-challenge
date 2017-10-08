-- add user

CREATE USER 'coding_challenge'@'%' IDENTIFIED BY 'coding_challenge';

GRANT ALL PRIVILEGES ON * . * TO 'coding_challenge'@'%';

FLUSH PRIVILEGES;