# Partiel Back-End "Material Manager"📦

Made by : **Arthur PHILIPPE**

## 📇 Description

Cette application Symfony permet la gestion des commandes et des matériaux. Il vise à offrir une solution complète pour la gestion des commandes, la sélection des matériaux et le suivi des stocks. Ce système permet aux utilisateurs de créer, modifier et gérer des commandes tout en assurant un suivi précis des quantités de matériaux disponibles.

## 📦 Prérequis

Avant de commencer, assurez-vous d'avoir installé [PHP](https://www.php.net/), [Composer](https://getcomposer.org/), et [Symfony CLI](https://symfony.com/download) sur votre machine.

pour vérifier si vous avez installé PHP, Composer et le CLI Symfony exécutez les commandes suivantes dans votre terminal :

```bash
php -v
composer -v
symfony -v
```

## 🛠️ Cloner le repo

Clonez le dépôt en utilisant Git :

```bash
git clone https://github.com/Voltoxx/partiel-backend.git
cd partiel-backend
```

## 🚀 Installer les dépendances

Installez les dépendances du projet :

```bash
composer install
```

## 🗄️ Configurer la base de données
Configurez votre base de données dans le fichier .env ou .env.local en modifiant la variable DATABASE_URL. Par exemple :

```makefile
DATABASE_URL="mysql://username:password@127.0.0.1:3306/material_manager"
```

Ensuite, créez la base de données et appliquez les migrations :

```bash 
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## 🔨 Démarrer l'application
Lancez l'application en mode développement :

```bash 
symfony server:start
```

Ouvrez [http://localhost:8000](http://localhost:8000) pour voir l'application dans votre navigateur.

Enjoy ! 🎉
