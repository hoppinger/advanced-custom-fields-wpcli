#!/bin/bash

if [ $# -lt 3 ]; then
  echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version]"
  exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}

WP_TESTS_DIR=${WP_TESTS_DIR-/tmp/wordpress-tests-lib}
WP_CORE_DIR=/private/tmp/wordpress/

create_folder() {
  rm -rf $WP_CORE_DIR
  mkdir -p $WP_CORE_DIR
}

install_wpcli() {
  cd $WP_CORE_DIR && curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
}

drop_database() {
  RESULT=`mysqlshow --user=$DB_USER --password=$DB_PASS $DB_NAME| grep -v Wildcard | grep -o $DB_NAME`

  if [ "$RESULT" == "$DB_NAME" ];
  then
    if [$DB_PASS == '']
    then
      mysqladmin -h $DB_HOST -u $DB_USER drop $DB_NAME
    else
      mysqladmin -h $DB_HOST -u $DB_USER -p $DB_PASS drop $DB_NAME
    fi
  fi
}

install_wp() {
  php $WP_CORE_DIR/wp-cli.phar core download --version=$WP_VERSION
  php $WP_CORE_DIR/wp-cli.phar core config --dbname="$DB_NAME" --dbuser="$DB_USER" --dbpass="$DB_PASS" --dbhost="$DB_HOST"
  php $WP_CORE_DIR/wp-cli.phar db create
  php $WP_CORE_DIR/wp-cli.phar core install --url='example.com' --title='Test Wordpress' --admin_user='admin' --admin_password='bestpasswordever' --admin_email='info@example.com'
}

install_acf() {
  read -e -p "ACF Path: " GF_DIR

  cp -rf $GF_DIR $WP_CORE_DIR/wp-content/plugins
  php $WP_CORE_DIR/wp-cli.phar plugin activate acf5-pro
}

create_symlink_to_acf_wpcli() {
  ln -s /Users/Marcel/Sites/Hoppinger/advanced-custom-fields-wpcli/advanced-custom-fields-wpcli $WP_CORE_DIR/wp-content/plugins
  php $WP_CORE_DIR/wp-cli.phar plugin activate advanced-custom-fields-wpcli
}

create_folder
install_wpcli
drop_database
install_wp
install_acf
create_symlink_to_acf_wpcli
