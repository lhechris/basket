# Tests — backend

Ce fichier décrit comment préparer l'environnement et exécuter la suite de tests PHP (PHPUnit) pour le backend.

## Prérequis

- PHP 7.4+ (ou version compatible avec vos dépendances)
- Composer
- sqlite3 (si vous utilisez la base SQLite fournie)

## Installation des dépendances

Depuis le répertoire `backend` :

Windows
```powershell
composer install
```

Linux / macOS
```bash
composer install
```

(Si PHPUnit n'est pas encore installé en dev)
```bash
composer require --dev phpunit/phpunit ^9.6 --no-interaction
```

## Configuration de l'environnement de test

Créez un fichier `.env` dans `backend/` (ou adaptez le chemin attendu par `env.php`) avec au minimum :

```
REPERTOIRE_DATA=../data/
ACTIVELOG=true
DBLOCATION=:memory:
```

- Ceci permet d'utiliser un BDD en mémoire afin de ne pas utiliser la BDD de production
- Il est possible de positionner un fichier dans DBLOCATION pour utiliser un BDD physque
- Le projet doit contenir `config/createdb.sql` qui initialise le schéma utilisé par les tests.


## Exécuter les tests

Depuis `backend` :

- Exécuter toute la suite de tests :
  Windows
  ```powershell
  .\vendor\bin\phpunit --testdox
  ```
  Linux / macOS
  ```bash
  ./vendor/bin/phpunit --testdox
  ```

- Exécuter un fichier de test spécifique :
  ```bash
  ./vendor/bin/phpunit --testdox tests/IndexTest.php
  ```

- Exécuter un test précis (filtre) :
  ```bash
  ./vendor/bin/phpunit --filter testUsers tests/IndexTest.php
  ```

## Tests HTTP d'intégration (optionnel)

Pour tester les endpoints via HTTP réel vous pouvez lancer le serveur PHP intégré :

```bash
cd backend/api
php -S 127.0.0.1:8000
```

Puis lancer des requêtes (curl / navigateur) vers `http://127.0.0.1:8000/?users` etc. Ces tests peuvent aussi être automatisés depuis PHPUnit (proc_open + file_get_contents).

## Isolation des tests

- Les tests devraient réinitialiser la base avant chaque cas (`setUp()` / `setUpBeforeClass()`). Si vos tests écrivent dans la DB, utilisez une DB de test dédiée et/ou transactions/rollback pour isoler les cas.
- Réinitialisez les superglobales (`$_GET`, `$_POST`, `$_SESSION`) et les buffers de sortie (ob_start / ob_get_clean) dans `setUp`/`tearDown` pour éviter les interférences entre tests.

## Dépannage rapide

- Erreur "Cannot redeclare ...": utilisez `include_once`/`require_once` ou assurez-vous que l'initialisation des fonctions / fichiers se fait une seule fois lors des tests (bootstrap PHPUnit recommandé).
- Si les tests ne trouvent pas la DB/schema : vérifiez `DBLOCATION` et que `config/createdb.sql` a bien été appliqué.


