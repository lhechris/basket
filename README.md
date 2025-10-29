# Basket

Ce fichier décrit comment préparer l'environnement, construire le site et exécuter la suite de tests PHP (PHPUnit) pour le backend. Il inclut aussi les étapes pour générer le `.env`, construire le site frontend (npm build) et copier le répertoire `backend/api` dans le build si nécessaire pour déploiement local/testing.

## Prérequis

- PHP 7.4+  
- Node.js + npm (pour le frontend Vite/Vue)
- sqlite3 (si vous utilisez la base SQLite fournie)

## Installation des dépendances

Le backend est autonome et n'utilise pas de dépendance.
Sauf pour l'execution des tests, pour celà voir : [backend](./backend/README.md)

Pour le frontend (depuis la racine du projet) :
```bash
cd site
npm install
```

## Génération du fichier .env

Créez un fichier `.env` dans `backend/`. Exemple minimal pour `backend/.env` :

```
REPERTOIRE_DATA=../data/
ACTIVELOG=true
DBLOCATION=../data/basket_test.db
```

Commandes pour créer le fichier automatiquement :

Windows (PowerShell)
```powershell
Set-Content -Path .\backend\.env -Value "REPERTOIRE_DATA=../data/`nACTIVELOG=true`nDBLOCATION=../data/basket_test.db"
```

Linux / macOS
```bash
cat > backend/.env <<'EOF'
REPERTOIRE_DATA=../data/
ACTIVELOG=true
DBLOCATION=../data/basket_test.db
EOF
```

## Builder le site (npm build)

Depuis le dossier `site` :
```bash
cd site
npm run build
```
La sortie (par défaut Vite) se trouve dans `site/dist`.

## Copier `backend/api` dans le build (optionnel)

Pour déployer localement l'API avec le build front, vous pouvez copier `backend/api` dans `site/dist/api` afin que les fichiers API soient disponibles avec le build (utile pour tests d'intégration locaux). Exemple :

Windows (PowerShell)
```powershell
# Nettoyer/Créer le répertoire cible
Remove-Item -Recurse -Force .\site\dist\api -ErrorAction SilentlyContinue
New-Item -ItemType Directory -Force .\site\dist\api
# Copier
Copy-Item -Path .\backend\api\* -Destination .\site\dist\api -Recurse -Force
```

Linux / macOS
```bash
rm -rf site/dist/api
mkdir -p site/dist/api
cp -r backend/api/* site/dist/api/
```

Remarque : adapter selon votre stratégie de déploiement. Le copy ci-dessus est pour un déploiement local/packaging simple.

## Initialiser la base de test

Si vous utilisez SQLite et `config/createdb.sql` :

Windows (PowerShell)
```powershell
sqlite3 ..\data\basket_test.db < config\createdb.sql
```

Linux / macOS
```bash
sqlite3 ../data/basket_test.db < config/createdb.sql
```

Pour inialiser les entrainements d'une saison modifier backend/init.php et lancer :
```bash
cd backend/api
php init.php
```


