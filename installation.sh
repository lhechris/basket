#!/usr/bin/env bash
set -euo pipefail

# === PARAMÈTRES ===
USER_HOME="/home/christophe"
BUILD_DIR="${USER_HOME}/builds/basket"
RELEASES_BUILD="${BUILD_DIR}/releases"

WEB_ROOT="/var/www/basket"
RELEASES_WEB="${WEB_ROOT}"
CURRENT_LINK="${WEB_ROOT}/u11"
SHARED="${WEB_ROOT}/data"


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
WORK_DIR="${USER_HOME}/workspace/basket"
cp -r ${WORK_DIR}/* ${NEW_BUILD}

echo ">>> Installation vuejs"
cd ${NEW_BUILD}/site
npm install
npm run build

cd ${NEW_BUILD}/backend
echo ">>> Installation Composer"
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# === SHARED ===
sudo mkdir -p "${SHARED}/uploads"

if [ ! -f "${SHARED}/database.db" ]; then
    sudo touch "${SHARED}/database.db"
    sudo sqlite3 "${SHARED}/database.db" < ${NEW_BUILD}/backend/config/createdb.sql
fi

if [ ! -f "${SHARED}/.env" ]; then
cat > "${BUILD_DIR}/.env" <<EOF
REPERTOIRE_DATA=../../data/
ACTIVELOG=true
DBLOCATION=../../data/database.db
TEMPLATE_FILE=../template.xlsx
EOF

sudo mv ${BUILD_DIR}/.env ${SHARED}
fi

# === COPIE VERS /var/www ===
echo "=== Copie vers ${NEW_WEB_RELEASE}"
sudo mkdir -p "${NEW_WEB_RELEASE}"
sudo cp -r ${NEW_BUILD}/site/dist/* ${NEW_WEB_RELEASE}/
sudo cp -r ${NEW_BUILD}/backend/api ${NEW_WEB_RELEASE}/
sudo cp -r ${NEW_BUILD}/backend/vendor ${NEW_WEB_RELEASE}/
sudo cp -r ${NEW_BUILD}/backend/templace.xlsx ${NEW_WEB_RELEASE}/


# === SYMLINK des éléments partagés ===
echo "=== Symlink shared ==="
sudo rm -f "${NEW_WEB_RELEASE}/database.db"
sudo ln -s "${SHARED}/database.db" "${NEW_WEB_RELEASE}/database.db"

sudo rm -rf "${NEW_WEB_RELEASE}/uploads"
sudo ln -s "${SHARED}/uploads" "${NEW_WEB_RELEASE}/uploads"

sudo rm -f "${NEW_WEB_RELEASE}/.env"
sudo ln -s "${SHARED}/.env" "${NEW_WEB_RELEASE}/.env"

# === PERMISSIONS ===
echo "=== Permissions ==="
sudo chown -R www-data:www-data "${NEW_WEB_RELEASE}"
sudo chmod -R 755 "${NEW_WEB_RELEASE}"

sudo chown www-data:www-data "${SHARED}/database.db"
sudo chmod 664 "${SHARED}/database.db"

# === Activation atomique ===
echo "=== Activation de la nouvelle release ==="
sudo ln -sfn "${TS}" "${CURRENT_LINK}"

# === Reload services ===
#echo "=== Reload PHP-FPM & apache ==="
#sudo systemctl reload "${PHP_FPM_SERVICE}"
#sudo systemctl reload "${WEB_SERVICE}"

echo "=== Déploiement terminé ==="
echo "Release active : ${TS}"

