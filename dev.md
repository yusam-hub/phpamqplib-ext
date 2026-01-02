#### testing php74

    docker exec -it dev-php74 sh -c "cd /var/www/php74/yusam-hub/redis-ext && exec bash"

    docker exec -it dev-php74 sh -c "cd /var/www/php74/yusam-hub/redis-ext && composer update"
    docker exec -it dev-php74 sh -c "cd /var/www/php74/yusam-hub/redis-ext && composer install"
    docker exec -it dev-php74 sh -c "cd /var/www/php74/yusam-hub/redis-ext && sh phpunit"
    docker exec -it dev-php74 sh -c "cd /var/www/php74/yusam-hub/redis-ext && git status"
    docker exec -it dev-php74 sh -c "cd /var/www/php74/yusam-hub/redis-ext && git pull"

    docker exec -it yusam-php74 sh -c "apk add php-sockets"
    docker exec -it yusam-php74 sh -c "cd /var/www/data/yusam/github/yusam-hub/phpamqplib-ext && composer update"
    docker exec -it yusam-php74 sh -c "cd /var/www/data/yusam/github/yusam-hub/phpamqplib-ext && sh phpunit"

    docker exec -it yusam-php74 sh -c "cd /var/www/data/yusam/github/yusam-hub/phpamqplib-ext/bin && php consumer.php queue1 tag1"
    docker exec -it yusam-php74 sh -c "cd /var/www/data/yusam/github/yusam-hub/phpamqplib-ext/bin && php consumer.php queue1 tag2"
    docker exec -it yusam-php74 sh -c "cd /var/www/data/yusam/github/yusam-hub/phpamqplib-ext/bin && php producer.php"



