#### dockers

    docker exec -it yusam-php74 bash
    docker exec -it yusam-php74 sh -c "htop"

    docker exec -it yusam-php74 sh -c "apk add php-sockets"
    docker exec -it yusam-php74 sh -c "cd /var/www/data/yusam/github/yusam-hub/phpamqplib-ext && composer update"
    docker exec -it yusam-php74 sh -c "cd /var/www/data/yusam/github/yusam-hub/phpamqplib-ext && sh phpunit"

    docker exec -it yusam-php74 sh -c "cd /var/www/data/yusam/github/yusam-hub/phpamqplib-ext/bin && php consumer.php queue1 tag1"
    docker exec -it yusam-php74 sh -c "cd /var/www/data/yusam/github/yusam-hub/phpamqplib-ext/bin && php consumer.php queue1 tag2"
    docker exec -it yusam-php74 sh -c "cd /var/www/data/yusam/github/yusam-hub/phpamqplib-ext/bin && php producer.php"



