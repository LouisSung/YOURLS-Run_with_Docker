#!/usr/bin/env bash
# Description: 
# Maintainer: [LouisSung](https://github.com/LouisSung)
# Version: v1.0.0 (2019, LS)

# Function
ask(){
    while true; do
        read -p "$(echo -e "\e[1;31m$1\e[0m")" yn
        if [[ -z $yn ]]; then yn=$2; fi
        case $yn in
            [Yy] ) echo "y"; break;;
            [Nn] ) echo "n"; break;;
            * ) echo -e "\e[1;31mErr:\e[0m Please answer y/n." >&2;;    # echo to stderr
        esac
    done
}

# Make sure this script execute from its location
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)"
RESTORE_DIR=$(pwd)
cd $SCRIPT_DIR
# <<<<<<< Script Start
cd docker/
docker-compose -f docker-compose-init.yml down
docker-compose down

# ask for remove folders (be careful)
if [ $(ask "Warning!! (Permanently) REMOVE folders for yourls and database? [y/N] " "N") = "y" ]; then
    echo -e '\e[1;33mremove yourls/ and database/\e[0m'
    sudo rm -r deploy/database/ deploy/yourls/
fi

# >>>>>>> Script End
cd $RESTORE_DIR
