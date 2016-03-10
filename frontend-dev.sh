#!/bin/bash

## hard link for CSS bundled file
rm -rf backend/web/css/index.css
ln frontend/public/assets/bundle.css backend/web/css/index.css

## hard link for JS bundled file
rm -rf backend/web/bundles/evaluationdashboard/js/index.js
ln frontend/public/assets/bundle.js backend/web/bundles/evaluationdashboard/js/index.js

## rely on vagrant NFS instead
rm -rf backend/web/fonts
rm -rf backend/web/images

echo "You are all set up!"
