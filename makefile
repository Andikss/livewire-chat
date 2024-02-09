.PHONY: dev install migrate rollback refresh clear

dev: install rollback refresh clear

composer-install:
	composer install

npm-install:
	npm install

migrate:
	php artisan migrate

rollback:
	php artisan migrate:rollback

refresh:
	php artisan migrate:refresh --seed

clear:
	php artisan view:clear && php artisan route:cache && php artisan route:clear && php artisan config:clear && php artisan optimize:clear && php artisan optimize && cls
