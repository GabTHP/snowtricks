# Projet 6 : Développez de A à Z le site communautaire Snow Tricks

Lien vers le projet : https://github.com/GabTHP/snowtricks

## Code Validation

[![SymfonyInsight](https://insight.symfony.com/projects/04a7926a-317a-4c4e-9a2e-c9d77f918b47/small.svg)](https://insight.symfony.com/projects/04a7926a-317a-4c4e-9a2e-c9d77f918b47)

Lien vers analyse du projet Symfony Insight : https://insight.symfony.com/projects/db671122-5df6-4f87-8e3e-d6823d6ac355

## Versions :

- Symfony : 5.4.4
- PHP : 7.4.3

## Installation du projet :

1. Télecharcher le repository snowtricks

2. Mettre a jour le fichier .env situé à la racine du projet :

   -Configuration du mailer avec le modèle ci-dessous : + MAILER_DSN=smtp://{EMAIL}:{MOT_DE_PASSE}@{SERVEUR_MAIL}:{PORT_SERVEUR_MAIL}

   -Configuration de la connexion à la base de données avec le modèle ci-dessous : + DATABASE_URL="mysql://{USER}:{MOT_DE_PASSE}@{BDD_HOST}:{PORT}/{NOM_BASE_DE_DONNEES}"

3. Placez dans à la racine du projet et lancer la commande ci-dessous pour installer et mettre à jours les composants :

   - composer install

4. Lancre les commandes ci-dessous pour créer la base de données :
   - php bin/console doctrine:database:create
   - php bin/console doctrine:migrations:migrate
5. Utilisez la commande ci-dessous pour générer un jeu de données et bénéficier d'une démo du site Snow Tricks ! -php bin/console doctrine:fixtures:load

Bonne visite !
