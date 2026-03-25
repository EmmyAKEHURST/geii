# Projet GEII

## À propos

Le département Génie Électrique et Informatique Industrielle _(GEII)_ de l'IUT souhaite se
doter d'un site web institutionnel moderne afin de centraliser l'ensemble de ses
informations à destination de ses différents publics : *futurs étudiants*, *entreprises partenaires*,
*étudiants inscrits* et *enseignants*.

Ce site viendra remplacer ou compléter l'offre de communication actuelle et constitue le
point d'entrée numérique officiel du département.

Ce projet n'est pas un projet officiel pour le GEII, mais nous ferons tout comme.

## Les objectifs du site

Le but de ce site sera de :

- valoriser l'offre de formation du département GEII _(DUT GEII, Licences Professionnelles)_.
- informer les entreprises sur les modalités de partenariat _(alternance, projets tuteurés)_.
- offrir aux étudiants inscrits un espace personnel sécurisé _(notes, emploi du temps, supports)_.
- permettre aux enseignants de gérer leurs ressources pédagogiques _(cours, notes, emploi du temps)_.
- garantir une administration centralisée du contenu par le personnel du département.

## Technologies utilisées

Pour la réalisation de ce projet, le framework Symfony ainsi que Bootstrap CSS seront utilisés.

## Équipe

- AKEHURST Emmy
- TAMDA Zohir

## Les règles de nommage

### PHP / classes (controllers, entities, services)

Concernant le nommage des classes, elles seront écrites en PascalCase (par exemple : `UserController`, `OrderRepository`).

Pour le nommage des fichiers sources PHP, elles correspondent aux noms des classes (par exemple : `UserController.php`).

En ce qui concerne les namespaces : correspond à l’arborescence via composer.json (`App\` pointe vers `src/`).

Tous les fichiers source possèdent un namespace au niveau du début de fichier. Dans le cadre de ce projet, le namespace `App\` pointe vers le dossier `src/`. Donc, pour un controller `src/Controller/HelloController.php`, le namespace sera `App\Controller\HelloController`.

### Les méthodes de contrôleur

Les méthodes sont généralement en camelCase (ex: `index()`, `show()`, `create()`). Sur Symfony, l'attribut `#[Route(...)]` sera systématiquement utilisée pour créer des routes accessibles.

### Les routes (URLs / noms de routes)

Les URL sont souvent en minuscules, avec des tirets ou non (ex: /admin/users-list).

Les paramètres d’URL : en snake_case et en minuscules (ex: /{user_id}).

### Twig

Les fichiers Twig finissent en `*.html.twig`.

### Les variables Twig et les données

Les variables passées au template sont généralement en camelCase (ex: `user`, `userProfile`).
