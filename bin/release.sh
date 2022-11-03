#!/bin/sh

# VARS - THESE SHOULD BE CHANGED!
ROOT_PATH=""

PRODUCT_NAME_GIT="acf-wp-cli-git"
PRODUCT_NAME_SVN="acf-wp-cli-svn"

SVN_REPO=""
GIT_REPO=""

# ASK INFO
echo "-------------------------------------------"
echo "        ACF WP-CLI        RELEASER         "
echo "-------------------------------------------"
read -p "VERSION: " VERSION
echo "-------------------------------------------"
read -p "PRESS [ENTER] TO RELEASE ACF WP-CLI VERSION "${VERSION}

create_git_release() {
  get_last_version_from_git
  remove_unwanted_files
}

get_last_version_from_git() {
  echo "Getting latest release from Github"
  rm -rf $ROOT_PATH/$PRODUCT_NAME_GIT

  mkdir $ROOT_PATH/$PRODUCT_NAME_GIT

  git clone $GIT_REPO $ROOT_PATH/$PRODUCT_NAME_GIT
}

remove_unwanted_files() {
  echo "Removing unwanted files"
  yes | rm -r $ROOT_PATH/$PRODUCT_NAME_GIT/.git
  yes | rm -r $ROOT_PATH/$PRODUCT_NAME_GIT/features

  rm $ROOT_PATH/$PRODUCT_NAME_GIT/.gitignore
  rm $ROOT_PATH/$PRODUCT_NAME_GIT/.travis.yml
  rm $ROOT_PATH/$PRODUCT_NAME_GIT/composer.json
  rm $ROOT_PATH/$PRODUCT_NAME_GIT/composer.lock
  rm $ROOT_PATH/$PRODUCT_NAME_GIT/phpunit.xml
  rm $ROOT_PATH/$PRODUCT_NAME_GIT/.phpcodesniffer.xml
  rm $ROOT_PATH/$PRODUCT_NAME_GIT/README.md
}

create_svn_release() {
  echo "Starting on SVN"

  create_repo
  update_svn
  copy_github_release
  get_readme
  do_svn_magic
}

update_svn() {
  svn update $ROOT_PATH/$PRODUCT_NAME_SVN
}

create_repo() {
  if [[ ! -d $ROOT_PATH/$PRODUCT_NAME_SVN ]];
  then
    echo "No SVN directory found, will do a checkout"
    svn checkout $SVN_REPO $ROOT_PATH/$PRODUCT_NAME_SVN
  fi

  if [[ -d $ROOT_PATH/$PRODUCT_NAME_SVN/trunk ]];
  then
    yes | rm -r $ROOT_PATH/$PRODUCT_NAME_SVN/trunk
    mkdir $ROOT_PATH/$PRODUCT_NAME_SVN/trunk
  fi
}

copy_github_release() {
  echo "Copying git release to SVN"
  cp -R $ROOT_PATH/$PRODUCT_NAME_GIT $ROOT_PATH/$PRODUCT_NAME_SVN/trunk/
}

get_readme() {
  echo "Creating readme"
  php -f $ROOT_PATH/bin/generate_readme.php
  cp $ROOT_PATH/readme.txt $ROOT_PATH/$PRODUCT_NAME_SVN/trunk/readme.txt
}

do_svn_magic() {
  echo "Doing some svn magic!"
  cd $ROOT_PATH/$PRODUCT_NAME_SVN
  svn add --force * --auto-props --parents --depth infinity -q

  # DO THE REMOVE ALL DELETED FILES UNIX COMMAND
  svn rm $( svn status | sed -e '/^!/!d' -e 's/^!//' ) $ROOT_PATH/$PRODUCT_NAME_SVN

  # COPY TRUNK TO TAGS/$VERSION
  svn copy trunk tags/${VERSION} $ROOT_PATH/$PRODUCT_NAME_SVN

  # DO A SVN STATUS
  svn status $ROOT_PATH/$PRODUCT_NAME_SVN

  # ASK FOR SVN COMMIT MESSAGE
  read -p "SVN COMMIT MESSAGE: " COMMIT_MESSAGE
  svn commit -m "$COMMIT_MESSAGE" $ROOT_PATH/$PRODUCT_NAME_SVN

  cd $ROOT_PATH
}

clean_up() {
  echo "Cleaning up!"
  yes | rm -r $ROOT_PATH/$PRODUCT_NAME_GIT
  yes | rm -r $ROOT_PATH/$PRODUCT_NAME_SVN
}

create_git_release
create_svn_release
clean_up
