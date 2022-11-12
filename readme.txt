Welcome to the read.me

for install : 



1. Creer .env.local :

DATABASE_URL="mysql://root:@127.0.0.1:3306/plateforme-facture?serverVersion=8&charset=utf8mb4"
APP_ENV=prod
MAILER_DSN=

2.composer install

3. yarn install 

4. yarn encore dev

4. php bin/console doctrine:database:create

5. php bin/console doctrine:migration:migrate

6. php bin/console doctrine:fixtures:load

7. php bin/console cache:clear

