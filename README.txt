PROJET GEII

A PROPOS
--------

Le département Génie Électrique et Informatique Industrielle (GEII) de l'IUT
souhaite se doter d'un site web institutionnel moderne afin de centraliser
l'ensemble de ses informations à destination de ses différents publics :
futurs étudiants, entreprises partenaires, étudiants inscrits et enseignants.

Ce site viendra remplacer ou compléter l'offre de communication actuelle et
constitue le point d'entrée numérique officiel du département.

Ce projet n'est pas un projet officiel pour le GEII, mais nous ferons tout
comme.

LES OBJECTIFS DU SITE
---------------------

Le but de ce site sera de :

- valoriser l'offre de formation du département GEII (DUT GEII, Licences
  Professionnelles).
- informer les entreprises sur les modalités de partenariat (alternance,
  projets tuteurés).
- offrir aux étudiants inscrits un espace personnel sécurisé (notes, emploi
  du temps, supports).
- permettre aux enseignants de gérer leurs ressources pédagogiques (cours,
  notes, emploi du temps).
- garantir une administration centralisée du contenu par le personnel du
  département.

TECHNOLOGIES UTILISEES
----------------------

Pour la réalisation de ce projet, le framework Symfony ainsi que Bootstrap
CSS seront utilisés.

EQUIPE
------

- AKEHURST Emmy
- TAMDA Zohir

================================================================================
INSTALLATION
================================================================================

Symfony CLI — Windows :
  Télécharger l'exécutable depuis https://symfony.com/download
  Placer symfony.exe dans le dossier du projet ou dans un dossier du PATH.

Symfony CLI — Linux :
  curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
  sudo apt install symfony-cli

  Ou via le script d'installation universel :
  curl -sS https://get.symfony.com/cli/installer | bash
  Puis déplacer le binaire : mv ~/.symfony5/bin/symfony /usr/local/bin/symfony


================================================================================
COMMANDES SYMFONY
================================================================================

Mettre une entité dans la base de données :
  php bin/console doctrine:schema:update --force

Créer une entité :
  php bin/console make:entity

Débuguer les routes :
  php bin/console debug:router

Démarrer le serveur local :
  .\symfony.exe local:server:start


================================================================================
COMPTES DE TEST
================================================================================

Format : rôle, mail, mot de passe.

Personnel :
  admin@geii.fr
  Admin@geii1

Enseignant :
  dupont.jean@geii.fr       Enseignant@1
  martin.sophie@geii.fr     Enseignant@1
  bernard.thomas@geii.fr    Enseignant@1

Etudiant :
  john@doe.fr                            Etudiant@1
  lambert.thomas@etudiant.iut.fr         Etudiant@1
  dupuis.alice@etudiant.iut.fr           Etudiant@1
  rousseau.maxime@etudiant.iut.fr        Etudiant@1
  moreau.celine@etudiant.iut.fr          Etudiant@1

Entreprise :
  contact@schneider-electric.fr    Entreprise@1
  contact@st.com                   Entreprise@1
  contact@thalesgroup.fr           Entreprise@1
