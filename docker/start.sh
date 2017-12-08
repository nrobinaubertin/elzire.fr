#!/bin/bash

# go to the root of the git repository
cdroot() {
    if ! [ -d ".git" ] && [ "$(pwd)" != "/" ]
    then
        cd ..
        cdroot
    fi
}

start_docker_service() {
    if [ -n "$(which systemctl 2>/dev/null)" ]
    then
        sudo systemctl start docker
        return
    fi
    if [ -n "$(which service 2>/dev/null)" ]
    then
        sudo service docker start
        return
    fi
}

isDockerRunning() {
    if [[ -n $(docker info 2>&1 1>/dev/null) ]] && [[ -z $(docker info) ]]
    then
        echo "the docker daemon is not running"
        exit 1
    fi
}

start_docker_service
isDockerRunning

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
    if [ "$1" = "--dev" ]
    then
        sudo docker run --init --rm -it -p 80:8080 -v "${pathToProject}":/www --name elzire elzire
    else
        sudo docker run --init -d -v "${pathToProject}":/www --name elzire elzire
    fi
fi
