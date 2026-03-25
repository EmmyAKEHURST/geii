# Projet GEII

## À propos

Le département Génie Électrique et Informatique Industrielle (GEII) de l'IUT souhaite se
doter d'un site web institutionnel moderne afin de centraliser l'ensemble de ses
informations à destination de ses différents publics : futurs étudiants, entreprises
partenaires, étudiants inscrits et enseignants.

Ce site viendra remplacer ou compléter l'offre de communication actuelle et constitue le
point d'entrée numérique officiel du département.

Ce projet n'est pas un projet officiel pour le GEII, mais nous ferons tout comme.

## Objectifs du site

Le but de ce site sera de :

- Valoriser l'offre de formation du département GEII (DUT GEII, Licences Professionnelles).
- Informer les entreprises sur les modalités de partenariat (alternance, projets tuteurés).
- Offrir aux étudiants inscrits un espace personnel sécurisé (notes, emploi du
temps, supports).
- Permettre aux enseignants de gérer leurs ressources pédagogiques (cours, notes, emploi du temps).
- Garantir une administration centralisée du contenu par le personnel du département.

## Technologies utilisées

Pour réaliser ce projet, nous utiliserons le framework Symfony (PHP) ainsi que Bootstrap (CSS) pour simplifier le travail côté design.

## Équipe

- AKEHURST Emmy
- TAMDA Zohir

## Règles de nommage

### HP / classes (controllers, entities, services)
Nommage des classes : PascalCase (ex: UserController, OrderRepository).
Nommage des fichiers : le nom du fichier correspond à la classe (ex: UserController.php).
Namespace : correspond à l’arborescence via composer.json (App\ pointe vers src/).
Donc une classe App\Controller\HelloController doit être dans src/Controller/HelloController.php.

### Méthodes de contrôleur
Les méthodes sont généralement en camelCase (ex: index(), show(), create()).
Les méthodes “action” peuvent être n’importe comment côté PHP, mais côté Symfony, utiliser surtout avec #[Route(...)].

### Routes (URLs / noms de routes)
URL (le chemin) : souvent en minuscules, avec tirets - (ex: /admin/users-list).
Les paramètres d’URL : en snake_case ou minuscules (ex: /{user_id}).
Nom de route (name: ...) : unique, souvent en camelCase ou snake_case/kebab-case selon le projet (le plus courant : user_list, admin_dashboard, etc.). L’important : c’est unique et lisible.

### Templates Twig
Les fichiers Twig finissent en .html.twig.
Conventions courantes :
soit un chemin explicite, ex: templates/admin/dashboard.html.twig
soit des noms descriptifs en kebab-case/snake_case (selon ton projet).
Pour réutiliser : extends "base.html.twig" puis remplir les {% block %}.

### Variables Twig / données
Les variables passées au template sont souvent en camelCase (ex: user, userProfile)
