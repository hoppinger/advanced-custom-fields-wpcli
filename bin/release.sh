#!/bin/sh

# ASK INFO
echo "-------------------------------------------"
echo "        ACF WP-CLI        RELEASER         "
echo "-------------------------------------------"
read -p "VERSION: " VERSION
echo "-------------------------------------------"
read -p "PRESS [ENTER] TO RELEASE VERSION THIS VERSION "${VERSION}

# VARS - THESE SHOULD BE CHANGED!
ROOT_PATH=""
PRODUCT_NAME=""
PRODUCT_NAME_GIT=${PRODUCT_NAME}"-git"
PRODUCT_NAME_SVN=${PRODUCT_NAME}"-svn"
SVN_REPO=""
GIT_REPO=""

# CHECKOUT SVN DIR IF NOT EXISTS
if [[ ! -d $PRODUCT_NAME_SVN ]];
then
  echo "No SVN directory found, will do a checkout"
  svn checkout $SVN_REPO $PRODUCT_NAME_SVN
fi

# DELETE OLD GIT DIR
rm -Rf $ROOT_PATH$PRODUCT_NAME_GIT

# CLONE GIT DIR
echo "Cloning GIT repo"
git clone $GIT_REPO $PRODUCT_NAME_GIT

# MOVE INTO GIT DIR
cd $ROOT_PATH$PRODUCT_NAME_GIT

# REMOVE UNWANTED FILES & FOLDERS
echo "Removing unwanted files"
rm -Rf .git
rm -f .gitignore
rm -f .travis.yml
rm -f package.json
rm -f composer.json
rm -f composer.lock
rm -f phpunit.xml
rm -f .phpcodesniffer.xml
rm -rf features

#CREATE NEW README
php -f bin/generate_readme.php

#COPY README TO TRUNK
cp readme.txt $ROOT_PATH$PRODUCT_NAME_SVN/readme.txt

# MOVE INTO SVN DIR
cd $ROOT_PATH$PRODUCT_NAME_SVN

# UPDATE SVN
echo "Updating SVN"
svn update

# DELETE TRUNK
echo "Replacing trunk"
rm -Rf trunk/

# COPY GIT DIR TO TRUNK
cp -R $ROOT_PATH$PRODUCT_NAME_GIT trunk/

# DO THE ADD ALL NOT KNOWN FILES UNIX COMMAND
svn add --force * --auto-props --parents --depth infinity -q

# DO THE REMOVE ALL DELETED FILES UNIX COMMAND
svn rm $( svn status | sed -e '/^!/!d' -e 's/^!//' )

# COPY TRUNK TO TAGS/$VERSION
svn copy trunk tags/${VERSION}

# DO A SVN STATUS
svn status

# ASK FOR SVN COMMIT MESSAGE
read -p "SVN COMMIT MESSAGE: " COMMIT_MESSAGE
svn commit -m "$COMMIT_MESSAGE"

# REMOVE THE GIT DIR
echo "Removing GIT dir"
rm -Rf $ROOT_PATH$PRODUCT_NAME_GIT

# REMOVE THE SVN DIR
echo "Removing SVN dir"
rm -Rf $ROOT_PATH$PRODUCT_NAME_SVN

# DONE, BYE
echo ${PRODUCT_NAME}" IS RELEASED"
