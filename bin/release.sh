#!/bin/sh

# THE GITHUB ACCESS TOKEN, GENERATE ONE AT: https://github.com/settings/applications (Personal access tokens)
GITHUB_ACCESS_TOKEN=""

# ASK INFO
echo "-------------------------------------------"
echo "      VERSION THIS        RELEASER         "
echo "-------------------------------------------"
read -p "VERSION: " VERSION
echo "-------------------------------------------"
read -p "PRESS [ENTER] TO RELEASE VERSION THIS VERSION "${VERSION}

# VARS - THESE SHOULD BE CHANGED!
ROOT_PATH="/Users/sebas/projects/wp-release/"
PRODUCT_NAME="versionthis"
PRODUCT_NAME_GIT=${PRODUCT_NAME}"-git"
PRODUCT_NAME_SVN=${PRODUCT_NAME}"-svn"
SVN_REPO="http://plugins.svn.wordpress.org/versionthis/"
GIT_REPO="git@github.com:sebastiaandegeus/versionthis.git"

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

# INIT&UPDATE&PULL SUBMODULE(S)
echo "Do the submodule dance"
git submodule init
git submodule update
git submodule foreach git checkout master && git pull

# REMOVE UNWANTED FILES & FOLDERS
echo "Removing unwanted files"
rm -Rf .git
#rm -Rf tests
#rm -f .gitattributes
rm -f .gitignore
#rm -f .gitmodules
#rm -f .travis.yml
#rm -f Gruntfile.js
#rm -f package.json
#rm -f .jscrsrc
#rm -f .jshintrc
#rm -f composer.json
#rm -f phpunit.xml
#rm -Rf admin/license-manager/.git

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

# REMOVE THE GIT DIR
echo "Removing SVN dir"
rm -Rf $ROOT_PATH$PRODUCT_NAME_SVN

# CREATE THE GITHUB RELEASE
echo "Creating GITHUB release"
#API_JSON=$(printf '{"tag_name": "%s","target_commitish": "master","name": "%s","body": "Release of version %s","draft": false,"prerelease": false}' $VERSION $VERSION $VERSION)
#curl --data "$API_JSON" https://api.github.com/repos/sebastiaandegeus/${PRODUCT_NAME}/releases?access_token=${GITHUB_ACCESS_TOKEN}

# DONE, BYE
echo "VERSION THIS RELEASER DONE"
