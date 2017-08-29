#!/bin/bash

# go to the root of the git repository
cdroot() {
    if ! [ -d ".git" ] && [ "$(pwd)" != "/" ]
    then
        cd ..
        cdroot
    fi
}

cdroot
# using the UID of php inside docker
HTTPDUSER=791
sudo setfacl -bR var  
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:"$(whoami)":rwX var
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:"$(whoami)":rwX var

cdroot
pathToProject=$(pwd)
cd docker || exit

sudo docker build \
    --build-arg PHP_EXT="php7-common php7-imagick php7-mbstring php7-gd php7-intl php7-iconv php7-json php7-dom php7-ctype php7-xml php7-posix php7-session php7-tokenizer php7-fileinfo" \
    -t elzire .

status=$?
if [ "$status" -eq 0 ]
then
    echo "$pathToProject"
    #sudo docker run --init -d -p 80:8080 -v "${pathToProject}":/www --name elzire elzire
    sudo docker run --init --rm -it -p 80:8080 -v "${pathToProject}":/www --name elzire elzire
fi
