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

### commande d'installation basique pour le lancement du projet 
  //composer create-project symfony/skeleton:"6.3.*" my_project_directory
    cd my_project_directory
    composer require webapp  
### prise en main du projet  
  symfony check:requirements
  composer install ou composer install --ignore-platform-reqs
  composer update
  composer require webapp  
  symfony server:start pour tester le site web
### commande base de donnée installation :
#### important bien configurer son .env locale pour plus d'infos regarder la doc symfony installation
    Database : regarder chaque command fais quoi exactement avant de les utiliser
    php bin/console doctrine:schema:update --force 
    php bin/console doctrine:schema:update                     
    php bin/console doctrine:schema:create  
    php bin/console doctrine:database:create                
   
### installation de tailwind command :
  composer require symfony/webpack-encore-bundle // composer require symfony/webpack-encore-bundle --ignore-platform-req=ext-zip
  npm install --force
  npm install -D tailwindcss postcss autoprefixer postcss-loader
  npx tailwindcss init -p

    dans le fichier tailwind.config.js ajouter dans content cest deux lignes  "./assests/**/*.js", "./templates/**/*.html.twig;",
    dans le fichier webpaxk.config.Js ajouter à la fin cette commande  .enablePostCssLoader()
    et enfin dans le dossier assets styles supprimer ce qu'il ya present et ajouter cette ligne :  
    @tailwind base;@tailwind components;@tailwind utilities;

 test la bonne installation de ton tailwind via la command  npm run build

email sender registrations confirmations devisbalu698@gmail.com mdp esgiChallenge a virer avant que adrian voit ça