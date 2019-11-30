#!/usr/bin/env bash
# Description: Init database ()
# Maintainer: [LouisSung](https://github.com/LouisSung)
# Version: v1.0.0 (2019, LS)

echo 'MySQL init...'
# check if folder `yourls` is empty and exit if not
if [ -n "$(ls -A /var/lib/mysql)" ]; then
    echo 'WARNING: Folder `/var/lib/mysql` is non-empty. Force stop MySQL init!!'
    exit 1
fi

# To fix: ERROR 1045 (28000): Access denied for user 'root'@'localhost' (using password: NO)
# ref: http://mustgeorge.blogspot.com/2011/11/mysql-error-1045-28000-using-password.html
docker-entrypoint.sh mysqld &    # run original command to init db and wait for a while
apt update && apt install procps -y    # for ps and pkill
sleep "${POSTPONE_FOR_A_WHILE:-40s}"
pkill mysqld
sleep 5s
mysqld_safe --user=root --skip-grant-tables --skip-networking >/dev/null 2>&1 &
sleep 5s

# To fix: Warning: mysqli::__construct(): (HY000/2054): The server requested authentication method unknown to the client in Standard input code on line 17
# ref: https://stackoverflow.com/a/50776838
echo 'update db user privileges'
echo "flush privileges;" >> init.sql
#echo "CREATE USER '$MYSQL_USER'@'%' IDENTIFIED WITH mysql_native_password BY '$MYSQL_PASSWORD';" >> /init.sql    # USER should have alread been created during start up
echo "ALTER USER '$MYSQL_USER'@'%' IDENTIFIED WITH mysql_native_password BY '$MYSQL_PASSWORD';" >> /init.sql
mysql -u root < /init.sql

echo 'MySQL init done!'

