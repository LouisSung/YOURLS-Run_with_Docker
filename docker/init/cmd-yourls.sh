#!/usr/bin/env bash
# Description: 
# Maintainer: [LouisSung](https://github.com/LouisSung)
# Version: v1.0.0 (2019, LS)

echo 'YOURLS init...'
# check if folder `yourls` is empty and exit if not
if [ -n "$(ls -A /yourls_init/)" ]; then
    echo 'WARNING: Folder `yourls` is non-empty. Force stop YOURLS init!!'
    exit 1
fi

# copy user provided config
cp -f /init/config.php /var/www/html/user/

# install useful plugins (modified (to compatible with yourls:1.7.4) by me :D)
if [ "$INIT_INSTALL_RECOMMENDED_PLUGINS" = true ]; then
    mv /var/www/html/user/plugins /var/www/html/user/plugins_backup
    cp -R /init/plugins/. /var/www/html/user/plugins
    mv /var/www/html/user/plugins_backup /var/www/html/user/plugins/plugins_backup
fi

# add (GitLab) OAuth sign in support (also written by me :D)
if [ "$INIT_ENABLE_GITLAB_OAUTH_SUPPORT" = true ]; then
    apk --update add git composer
    composer require omines/oauth2-gitlab:^3.1.2
    git clone https://github.com/LouisSung/YOURLS-OAuth_Sign_In.git /var/www/html/user/plugins/yourls-oauth_sign_in
fi

# copy all files from inner folder `html/` to outer `yourls/`
cp -R /var/www/html/. /yourls_init/

echo 'YOURLS init done!'

