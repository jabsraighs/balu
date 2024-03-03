# balu
## Contributeur & fonctionnalité
   Ulysseassoo - ASSO'O EMANE Ulysse : 
   - Mise en production du site
   - Création des composants tables, bloc de facture, titre de la page, carte de graphique
   - Design de l'application
   - Refonte des pages et tableaux
   
   jabsraighs - BOUAKI Arthur : 
   - Génération de pdf pour les factures et les devis
   - Fonctionnalités de la partie ADMIN (CRUD)
   - CRUD des factures et des devis
   - Envoie d'emails automatique

   
# Mise en production
[Lien de la mise en production](https://www.main-bvxea6i-6mctjmxjf3yrq.fr-3.platformsh.site/)
# Commande Symfony
## Site 
installer symfony regarder la doc symfony
https://symfony.com/doc/current/index.html 
pour la base de donnée regarder le make file 

### commande d'installation basique pour le lancement du projet ("creation du projet")
  //composer create-project symfony/skeleton:"6.3.*" my_project_directory
    cd my_project_directory
   // composer require webapp  
### prise en main du projet  
  symfony check:requirements
  composer install ou composer install --ignore-platform-reqs
  composer update
 // composer require webapp  
### installation de tailwind command :
  // composer require symfony/webpack-encore-bundle // composer require symfony/webpack-encore-bundle --ignore-platform-req=ext-zip

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
 
*/

### commande base de donnée installation :
#### important bien configurer son .env.local pour plus d'infos regarder la doc symfony installation
https://symfony.com/doc/current/configuration.html allez dans la partie selecting active Environnement
outils de debug :  php bin/console debug:dotenv
  Database : regarder chaque command fais quoi exactement avant de les utiliser
  php bin/console doctrine:database:create 
  php bin/console doctrine:schema:update --force 
  // recap des commandes sont à utilisée en fct de la situation
  //  php bin/console doctrine:schema:update
  //  php bin/console doctrine:schema:create  
  //  php bin/console doctrine:database:create 
Fix error duplicate table (en rapport avec le cours uniquement; quand vous faites un pull du git du cours)
Supprimer l'ensemble de vos migrations et faire les commandes suivantes :

docker compose exec     
php bin/console d:d:d --force 
php bin/console d:d:c 
php bin/console make:migr 
php bin/console d:m:m

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