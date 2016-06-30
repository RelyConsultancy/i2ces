#!/bin/bash
echo '

### OS Details'
cat /etc/redhat-release

echo '

### Kernel Details'
uname -a
echo '

### PDF symfony parameters'
php app/console debug:parameters | grep 'pdf\|url_base'

echo '

### Apache Details'
httpd -V

echo '

### PhantomJs Version'
phantomjs -v

echo '

### NPM Version'
npm -v

echo '

### Node Version'
node -v