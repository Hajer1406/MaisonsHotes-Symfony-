# Rapport Analytique : Système de Gestion de Maisons d'Hôtes en Tunisie

## Sommaire
1. [Présentation du projet](#1-présentation-du-projet)
2. [Analyse fonctionnelle](#2-analyse-fonctionnelle)
3. [Analyse technique](#3-analyse-technique)
4. [Diagrammes UML](#4-diagrammes-uml)
5. [Workflow métier](#5-workflow-métier)
6. [Conclusion](#6-conclusion)

---

## 1. Présentation du projet

### 1.1. Nom du projet
**MaisonsHotes-Symfony**

### 1.2. Contexte
Dans le cadre de la valorisation du patrimoine touristique en Tunisie, le secteur des maisons d'hôtes connaît une croissance exponentielle. La gestion manuelle ou fragmentée des réservations, des clients et des propriétaires constitue un frein majeur à la rentabilité et à l'expérience utilisateur.

### 1.3. Objectifs
L'objectif principal de ce projet est de concevoir et de développer une plateforme web robuste et centralisée permettant de :
- Digitaliser et simplifier la réservation de maisons d'hôtes.
- Offrir une interface intuitive aux utilisateurs et clients.
- Mettre à la disposition des administrateurs un back-office performant pour la gestion intégrale de l'écosystème (maisons, propriétaires, clients, réservations).
- Intégrer des fonctionnalités avancées comme la génération de factures PDF, l'envoi d'emails et un chatbot d'assistance.

### 1.4. Problématique résolue
Le système résout la problématique de la décentralisation de l'information et des erreurs humaines inhérentes à la gestion manuelle. Il assure la sécurité des données, l'intégrité temporelle des réservations et fluidifie la communication entre les différents acteurs.

---

## 2. Analyse fonctionnelle

### 2.1. Acteurs du système
- **Visiteur** : Utilisateur non authentifié pouvant consulter et rechercher des maisons.
- **Client (Utilisateur connecté)** : Utilisateur disposant d'un compte, capable d'effectuer des réservations et de gérer ses favoris.
- **Administrateur** : Super-utilisateur gérant les entités via le tableau de bord (EasyAdmin).

### 2.2. Fonctionnalités principales
- **Gestion des Comptes** : Inscription, authentification sécurisée, réinitialisation de mot de passe.
- **Catalogue de Maisons** : Affichage, recherche multicritères et gestion des favoris.
- **Réservations** : Processus de réservation sécurisé avec vérification de disponibilité, et génération de reçus PDF.
- **Back-Office (Administration)** : Opérations CRUD complètes sur les Clients, Maisons, Propriétaires, Utilisateurs et Réservations.
- **Support Automatisé** : Interface Chatbot pour assister les utilisateurs.

### 2.3. Cas d'utilisation (Use Cases)
- *S'inscrire / Se connecter*
- *Rechercher une maison d'hôte*
- *Réserver un séjour*
- *Gérer les utilisateurs et les rôles (Admin)*
- *Gérer le contenu du site (Admin)*

---

## 3. Analyse technique

### 3.1. Architecture Symfony MVC
Le projet adopte strictement le design pattern MVC (Modèle-Vue-Contrôleur) offert par le framework PHP Symfony, garantissant la séparation des préoccupations :
- **Modèle** : Gestion des données via Doctrine ORM.
- **Vue** : Rendu des interfaces via le moteur de templates Twig.
- **Contrôleur** : Traitement des requêtes HTTP et orchestration de la logique applicative.

### 3.2. Structure des dossiers
- `src/Entity/` : Classes PHP représentant les tables de la base de données (ex: `User`, `Maison`, `Reservation`).
- `src/Controller/` : Classes gérant les routes et la logique des pages (ex: `ReservationController`, `LoginController`).
- `src/Form/` : Classes définissant les formulaires Symfony (ex: `RegistrationFormType`, `MaisonType`).
- `templates/` : Fichiers `.html.twig` structurant l'interface utilisateur.
- `config/` : Fichiers YAML configurant les packages, les services et la sécurité.
- `public/` : Point d'entrée de l'application (`index.php`) et ressources statiques (CSS, JS, images).

### 3.3. Configuration et Routes
La configuration s'appuie massivement sur les attributs PHP 8 (ex: `#[Route('/maison', name: 'app_maison')]`) pour la déclaration des routes directement au-dessus des méthodes de contrôleurs, offrant une lisibilité optimale et une maintenance simplifiée.

### 3.4. Entités Doctrine
L'ORM Doctrine gère la persistance relationnelle. Principales entités :
- `User` : Hérite de `UserInterface`, gère l'authentification.
- `Client` & `Proprietaire` : Gèrent les informations personnelles.
- `Maison` : Contient les détails de la propriété (prix, ville, image).
- `Reservation` : Entité de jointure logique entre `Client` et `Maison` incluant les dates de séjour.

### 3.5. Sécurité et Authentification
- Géré par le composant `Security` de Symfony.
- Mots de passe hachés.
- Protection contre les failles CSRF (implémentée via les formulaires Symfony).
- Hiérarchie des rôles (ex: `ROLE_USER`, `ROLE_ADMIN`) protégeant l'accès aux routes sensibles (EasyAdmin protégé).

### 3.6. Base de données MySQL
Modélisée de façon relationnelle : relation `OneToMany` entre `Proprietaire` et `Maison`, `ManyToOne` entre `Reservation`, `Client` et `Maison`, et `ManyToMany` pour la liste des favoris (`User` - `Maison`).

---

## 4. Diagrammes UML

Les diagrammes ont été générés sous format Mermaid (fichiers `.mmd` dans le répertoire `diagrams/`).

### 4.1. Diagramme de Cas d'Utilisation
Illustre les interactions entre les acteurs (Visiteur, Utilisateur, Admin) et les fonctionnalités du système (fichier `diagrams/use_case.mmd`).

### 4.2. Diagramme de Classes
Définit la structure statique du système, les attributs et les associations complexes entre les entités métier (Client, Maison, Reservation, etc.) (fichier `diagrams/class.mmd`).

### 4.3. Diagramme de Séquence
Modélise la chronologie des opérations lors du processus critique de réservation d'une maison d'hôte (fichier `diagrams/sequence.mmd`).

### 4.4. Diagramme d'Activité
Représente le flux d'exécution et les conditions logiques depuis la recherche d'une maison jusqu'à la validation du paiement et la génération de la facture (fichier `diagrams/activity.mmd`).

---

## 5. Workflow métier

### 5.1. Authentification
L'utilisateur s'inscrit (`RegistrationController`), ses données sont validées (Contraintes de validation Symfony) et son mot de passe est haché avant insertion. Pour se connecter (`LoginController`), le système vérifie les identifiants et génère un jeton de session sécurisé.

### 5.2. Recherche et Réservation
1. **Recherche** : L'utilisateur accède à la liste des maisons ou utilise le formulaire de recherche (géré par `MaisonSearchType`).
2. **Sélection** : Il consulte les détails (Vue `show.html.twig`).
3. **Réservation** : S'il est authentifié, il soumet ses dates. Le `ReservationController` vérifie la validité des dates, lie la réservation au Client connecté et à la Maison, puis persiste en base.
4. **Facturation** : Le système utilise un service ou un contrôleur dédié (ex: `pdf.html.twig`) pour générer le reçu sous format PDF.

### 5.3. Gestion Administrateur
L'administrateur accède au back-office situé sous `/admin` (sécurisé). EasyAdminBundle génère automatiquement des interfaces CRUD (`CrudController`) riches permettant de valider, modifier ou supprimer des entités (Maisons, Clients, Utilisateurs) sans interagir directement avec la base de données.

---

## 6. Conclusion
Ce projet démontre une utilisation experte et académique du framework Symfony. En appliquant rigoureusement le modèle MVC, en sécurisant les accès et en utilisant l'ORM Doctrine pour modéliser une base de données relationnelle complexe, l'application constitue une solution fiable, évolutive et professionnelle pour la gestion de l'écosystème des maisons d'hôtes en Tunisie.
