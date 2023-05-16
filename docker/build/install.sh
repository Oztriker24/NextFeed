#!/bin/bash

ROOT_DIR=/var/www/html
API_DIR=$ROOT_DIR/symfony

if [ -d $API_DIR -a ! -d $API_DIR/vendor ]; then
    app_initial_install=1
fi

#echo ">>> According permissions..."
#chown -R www-data:www-data $API_DIR

# Symfony install
if [ -d $API_DIR ]; then

    cd $API_DIR


    if [ -n "$app_initial_install" ]; then

      COMPOSER_OPTIONS="--no-ansi --no-interaction --no-progress --optimize-autoloader"
      echo ">>> Installing Composer packages..."
      echo "composer install $COMPOSER_OPTIONS"
      composer install $COMPOSER_OPTIONS

    else

      echo ">>> Updating Composer packages..."
      echo "composer update"
      composer update

    fi

fi

# Services startup
service apache2 restart

echo ">>> All done!"

/bin/bash