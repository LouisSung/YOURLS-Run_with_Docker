#!/usr/bin/env bash
# Description: 
# Maintainer: [LouisSung](https://github.com/LouisSung)
# Version: v1.0.0 (2019, LS)

# Make sure this script execute from its location
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" >/dev/null 2>&1 && pwd)"
RESTORE_DIR=$(pwd)
cd $SCRIPT_DIR
# <<<<<<< Script Start
cd docker/
docker-compose -f docker-compose-init.yml up
echo 'Init complete'
docker-compose -f docker-compose-init.yml down

# >>>>>>> Script End
cd $RESTORE_DIR
