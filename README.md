# balu
## Contributeur
  blandine-228
  aalomatsi@myges.fr

   uassooemane@myges.fr
   Ulysseassoo

   Lyzabouzar@gmail.com
   Lbouzar
   
   abouaki1@myges.fr
   jabsraighs

   
# Commande Symfony
## Site 
installer symfony regarder la doc symfony
https://symfony.com/doc/current/index.html 
pour la base de donnée regarder le make file 

### commande d'installation basique pour le lancement du projet ("creation du projet")
  //composer create-project symfony/skeleton:"6.3.*" my_project_directory
    cd my_project_directory
    composer require webapp  
### prise en main du projet  
  symfony check:requirements
  composer install ou composer install --ignore-platform-reqs
  composer update
  composer require webapp  
### installation de tailwind command :
  composer require symfony/webpack-encore-bundle // composer require symfony/webpack-encore-bundle --ignore-platform-req=ext-zip
  npm install --force
  npm install -D tailwindcss postcss autoprefixer postcss-loader
  npx tailwindcss init -p
     dans le fichier tailwind.config.js ajouter dans content c'est deux lignes :
     "./assests/**/*.js", "./templates/**/*.html.twig;"
     dans le fichier webpaxk.config.Js ajouter à la fin cette commande  
     .enablePostCssLoader()
     et enfin dans le dossier assets styles supprimer ce qu'il ya present et ajouter cette ligne :  
     @tailwind base;@tailwind components;@tailwind utilities;

 test la bonne installation de ton tailwind via la command npm run dev puis npm run build
 


### commande base de donnée installation :
#### important bien configurer son .env.locale pour plus d'infos regarder la doc symfony installation
  Database : regarder chaque command fais quoi exactement avant de les utiliser
  php bin/console doctrine:database:create 
  php bin/console doctrine:schema:update --force 
  // les commandes sont à utilisée en fct de la situation
  //  php bin/console doctrine:schema:update                     
  //  php bin/console doctrine:schema:create  
  //  php bin/console doctrine:database:create 

## MailDev nécessaire pour le test d'inscription d'utilisateur
commande pour installer maildev 
npm i maildev 
email sender registrations confirmations devisbalu698@gmail.com mdp esgiChallenge a virer avant que adrian voit ça
maildev : confirmation Email Login 
commande : 
  maildev
  tuer le maildev en cas d'erreur disant que le maidev est deja en cours 
  il faut se mettre dans l'invite de commande en mode admin 
  netstat -ano | findstr :<PORT>
  taskkill /PID <PID> /F

Symfony server:start pour tester le site web

## Fixtures command :
  php bin/console doctrine:fixtures:load 
  php bin/console doctrine:fixtures:load --group=UserFixtures
  // pas necesssaire
  composer require --dev orm-fixtures
  composer require fakerphp/faker --dev
  php bin/console doctrine:fixtures:load --purge-exclusions=post_category --purge-exclusions=comment_type