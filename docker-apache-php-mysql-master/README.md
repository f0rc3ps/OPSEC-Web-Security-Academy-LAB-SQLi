# docker-apache-php-mysql

Docker example with Apache, MySql 8.0, PhpMyAdmin and Php

I use docker-compose as an orchestrator. To run these containers:

```
docker-compose up -d
```

Open phpmyadmin at [http://localhost:8092](http://localhost:8092)
Open web browser to look at a simple php example at [http://localhost:8091](http://localhost:8091)

Run mysql client:

- `docker-compose exec db mysql -u root -p` 

Enjoy !
