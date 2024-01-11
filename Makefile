install:
	composer install

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src public

PORT ?= 8000

start:
	php -S 0.0.0.0:$(PORT) -t public public/index.php