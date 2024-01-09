install:
	composer install

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src public

start:
	php -S localhost:8080 -t public public/index.php