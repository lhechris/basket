#!/usr/bin/env bash
set -euo pipefail

# === PARAMÈTRES ===
USER_HOME="/home/clhermit"
BUILD_DIR="${USER_HOME}/builds/basket"
RELEASES_BUILD="${BUILD_DIR}/releases"
SHARED="${BUILD_DIR}/shared"

WEB_ROOT="${BUILD_DIR}/var/www/basket"
RELEASES_WEB="${WEB_ROOT}"
CURRENT_LINK="${WEB_ROOT}/current"

WORK_DIR="${USER_HOME}/Workspace/basket"

#REPO="https://votre-repo-git.git"
#BRANCH_OR_TAG="main"

#PHP_VERSION="8.4"
#PHP_FPM_SERVICE="php${PHP_VERSION}-fpm"
#WEB_SERVICE="apache2"

TS=$(date +"%Y-%m-%d-%H%M%S")
NEW_BUILD="${RELEASES_BUILD}/${TS}"
NEW_WEB_RELEASE="${RELEASES_WEB}/${TS}"

echo "=== Build dans ${NEW_BUILD}"
mkdir -p "${NEW_BUILD}"
echo ">>> Récupération du code"
#git clone "${REPO}" .
#git checkout "${BRANCH_OR_TAG}"
cp -r ${WORK_DIR}/* ${NEW_BUILD}

echo ">>> Installation vuejs"
cd ${NEW_BUILD}/site
npm install
npm run build

cd ${NEW_BUILD}/backend
echo ">>> Installation Composer"
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# === SHARED ===
mkdir -p "${SHARED}/uploads"

if [ ! -f "${SHARED}/database.sqlite" ]; then
    touch "${SHARED}/database.sqlite"
fi

if [ ! -f "${SHARED}/.env.local" ]; then
cat > "${SHARED}/.env.local" <<EOF
REPERTOIRE_DATA=../../data/
ACTIVELOG=true
DBLOCATION=../../data/basketu11.db
EOF
fi

# === COPIE VERS /var/www ===
echo "=== Copie vers ${NEW_WEB_RELEASE}"
sudo mkdir -p "${NEW_WEB_RELEASE}"
sudo cp -r "${NEW_BUILD}/site/dist/*" "${NEW_WEB_RELEASE}/"
sudo cp -r "${NEW_BUILD}/site/backend/api" "${NEW_WEB_RELEASE}/"
sudo cp -r "${NEW_BUILD}/site/backend/vendor" "${NEW_WEB_RELEASE}/"


# === SYMLINK des éléments partagés ===
echo "=== Symlink shared ==="
sudo rm -f "${NEW_WEB_RELEASE}/database.sqlite"
sudo ln -s "${SHARED}/database.sqlite" "${NEW_WEB_RELEASE}/database.sqlite"

sudo rm -rf "${NEW_WEB_RELEASE}/uploads"
sudo ln -s "${SHARED}/uploads" "${NEW_WEB_RELEASE}/uploads"

sudo rm -f "${NEW_WEB_RELEASE}/.env.local"
sudo ln -s "${SHARED}/.env.local" "${NEW_WEB_RELEASE}/.env.local"

# === PERMISSIONS ===
echo "=== Permissions ==="
sudo chown -R www-data:www-data "${NEW_WEB_RELEASE}"
sudo chmod -R 755 "${NEW_WEB_RELEASE}"

sudo chown www-data:www-data "${SHARED}/database.sqlite"
sudo chmod 664 "${SHARED}/database.sqlite"

# === Activation atomique ===
echo "=== Activation de la nouvelle release ==="
sudo ln -sfn "${NEW_WEB_RELEASE}" "${CURRENT_LINK}"

# === Reload services ===
#echo "=== Reload PHP-FPM & apache ==="
#sudo systemctl reload "${PHP_FPM_SERVICE}"
#sudo systemctl reload "${WEB_SERVICE}"

echo "=== Déploiement terminé ==="
echo "Release active : ${TS}"