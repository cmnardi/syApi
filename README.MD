# Symfony application 

Symfony 5 application with Docker envirioment

## Docker envirioment 
The docker envirioment was created on docker folder, to initialize run
```bash
cd docker
docker-compose up -d 
```

## Migrations and fixtures
To run the application you have to execute migrations and fixtures to load the database structure and default data
```bash
docker exec -it sf5_3 php bin/console doctrine:migration:migrate -vvv
docker exec -it sf5_3 php bin/console doctrine:fixtures:load -vvv --env=test
```

## Tests
You can run the tests 
```bash
docker exec -it sf5_3 php bin/console doctrine:migration:migrate --env=test 
docker exec -it sf5_3 php bin/console doctrine:fixtures:load --env=test
docker exec -it sf5_3 php vendor/phpunit/phpunit/phpunit
```

## Documentation
The api documentation is avaiable [here](http://localhost/api/doc) and one collection is saved with examples *thunder-collection_sf5.json*