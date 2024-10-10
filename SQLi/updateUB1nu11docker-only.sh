#!/usr/bin/bash
cd ${HOME}

# Stop for updating
docker stop ub1nu11
docker compose -f ~/ub1nu11/docker/docker-compose.yml down -v
echo "Please wait..."
sleep 5;
rm -rf ub1nu11

# get ub1nu11 
git clone https://github.com/nu11secur1ty/ub1nu11.git

# set ip
cd ub1nu11/docker/
chmod a+x setenvip.sh
bash setenvip.sh
cd ..
docker-compose -f docker/docker-compose.yml up -d

cd ${HOME}
exit 0;
