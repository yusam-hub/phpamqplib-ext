#### yusam-hub/redis-ext

    "php": "^7.4|^8.0|^8.1|^8.2"

#### tests

    sh phpinit

#### setup

    "repositories": {
        ...
        "yusam-hub/phpamqplib-ext": {
            "type": "git",
            "url": "https://github.com/yusam-hub/phpamqplib-ext.git"
        }
        ...
    },
    "require": {
        ...
        "yusam-hub/phpamqplib-ext": "dev-master"
        ...
    }