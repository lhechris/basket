#!/bin/bash

if [ ! -d dist ]; then 
	mkdir dist
fi

rm -rf dist/*

cd site
yarn run build
cp -r dist/* ../dist
cp -r ../backend/api ../dist
cd ..
sed -i 's|../../data|../data|g' dist/api/.env

