# tools
Geek event tools

[![Coverage Status](https://coveralls.io/repos/github/geekevent/tools/badge.svg?branch=main)](https://coveralls.io/github/geekevent/tools?branch=main)

commande utile :

builder le container :
```shell script
docker-compose build php-fpm
```

se connecter au container PHP 
```shell script
docker-compose exec -u www-data php-fpm bash
```

démarrer le projet 
```shell script
docker-compose up -d
```

arrêter le projet 
```shell script
docker-compose down
```

__dans le container php__

mettre à jour les paquets 
```shell script
composer install
```

mettre à jour la base de donnée:
```shell script
bin/console do:sc:up -f
```

jouer les tests unitaire :
```shell script
vendor/bin/phpunit
```

jouer les tests behat :
```shell script
vendor/bin/behat
```

appliquer le linter PHP:
```shell script
./cs-fixer
```

Appliquer php-stan
```shell script
./php-stan
```