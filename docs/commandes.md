# Les commandes Symfony

Pour mettre une entité dans la base de données :

```sh
php bin/console doctrine:schema:update --force
```

Pour créer une entité :

```sh
    php bin/console make:entity
```

Pour débuguer les routes : 

```sh
    php bin/console debug:router  
```

Pour démarrer le serveur local :

```powershell
    .\symfony.exe local:server:start
```