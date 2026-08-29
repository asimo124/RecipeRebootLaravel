-- Applied on first init of an empty MySQL data volume.
-- Also re-applied manually if the recipes user loses CREATE DATABASE rights.
GRANT ALL PRIVILEGES ON *.* TO 'recipes'@'%';
FLUSH PRIVILEGES;
