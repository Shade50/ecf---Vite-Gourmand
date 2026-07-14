# 🍽️ Vite & Gourmand

Projet réalisé dans le cadre de l'Évaluation en Cours de Formation (ECF) du titre professionnel **Développeur Web et Web Mobile (DWWM)** chez Studi.

L'objectif est de développer une application web permettant à des particuliers de commander des menus gastronomiques pour des événements (anniversaires, mariages, séminaires, etc.) avec une gestion complète des commandes, des utilisateurs et de l'administration.

---

# 🚀 Fonctionnalités

## Front Office

- Consultation des menus
- Authentification des utilisateurs
- Création d'un compte
- Gestion du profil utilisateur
- Historique des commandes
- Détail d'une commande
- Commande d'un menu
- Calcul automatique du prix
- Calcul des frais de livraison
- Livraison calculée avec GeoPF + OSRM
- Gestion des stocks
- Formulaire de contact

---

## Administration

- Gestion des menus
- Gestion des plats
- Gestion des thèmes
- Gestion des allergènes
- Gestion des informations du restaurant
- Gestion des horaires
- Tableau de bord administrateur

---

## Technologies utilisées

- PHP 8.3
- Symfony 7
- Doctrine ORM
- Twig
- Bootstrap 5
- JavaScript
- MySQL
- GeoPF API
- OSRM API
- Docker *(en cours d'intégration)*
- Git / GitHub

---

# 📦 Installation

## Cloner le dépôt

```bash
git clone https://github.com/Shade50/ecf---Vite-Gourmand.git
```

Puis :

```bash
cd ecf---Vite-Gourmand/vite-gourmand
```

---

## Installer les dépendances

```bash
composer install
```

---

## Configurer l'environnement

Créer un fichier :

```
.env.local
```

Configurer notamment :

```
DATABASE_URL=
APP_ENV=dev
APP_SECRET=
```

---

## Créer la base de données

```bash
php bin/console doctrine:database:create
```

---

## Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate
```

---

## Charger les données de démonstration

```bash
php bin/console doctrine:fixtures:load
```

---

## Lancer le serveur

Avec Symfony CLI :

```bash
symfony serve
```

ou

```bash
php -S localhost:8000 -t public
```

---

# 📁 Arborescence simplifiée

```
src/
 ├── Controller/
 ├── Entity/
 ├── Form/
 ├── Repository/
 ├── Service/
 ├── Security/

templates/

public/

config/

migrations/
```

---

# 👥 Comptes de démonstration

Des comptes de démonstration peuvent être créés via les Fixtures.

Exemple :

- Administrateur
- Employé
- Utilisateur

*(Les identifiants seront renseignés avant la livraison finale.)*

---

# 🚚 Livraison

Le calcul de la livraison est réalisé automatiquement grâce à :

- Géocodage des adresses avec **GeoPF**
- Calcul d'itinéraire avec **OSRM**
- Calcul des frais :

```
5 € + 0,59 €/km
```

L'adresse du restaurant est entièrement configurable depuis l'administration.

---

# 🔐 Sécurité

- Authentification Symfony
- Hashage des mots de passe
- Gestion des rôles :

- ROLE_ADMIN
- ROLE_EMPLOYER
- ROLE_USER

- Protection des routes sensibles

---

# 📋 Fonctionnalités principales

- Authentification
- Gestion des utilisateurs
- Gestion du catalogue
- Gestion des commandes
- Gestion du stock
- Livraison dynamique
- Administration
- Profil utilisateur

---

# 📚 Documentation

Les documents suivants accompagnent ce projet :

- Manuel utilisateur
- Documentation technique
- Documentation de gestion de projet
- Charte graphique

---

# 👨‍💻 Auteur

**Jérôme Labbé**

Titre Professionnel  
Développeur Web et Web Mobile

Projet réalisé dans le cadre de l'ECF Studi.

2026