#!/usr/bin/bash

echo "### Preparing your OS for installation on AlmaLinux, Fedora, Debian, openSUSE Tumbleweed, Rocky Linux and Ubuntu. OS's"
echo
sleep 5;

### Clean and prepare the ENV
systemctl stop docker.socket
systemctl stop docker.service

sleep 3;
for pkg in docker.io docker-doc docker-compose docker-compose-v2 podman-docker containerd runc; do sudo apt-get remove $pkg; done
rm -rf /var/lib/docker
rm -rf /etc/docker

### Install Docker
apt autoremove -y
apt update && apt upgrade && apt autoremove -y
sudo curl -s https://raw.githubusercontent.com/nu11secur1ty/Docker/main/installer_24.04.sh | sh
echo "### Starting the Docker service...###"
systemctl start docker.service

# get ub1nu11 
git clone https://github.com/nu11secur1ty/ub1nu11.git

# set ip
cd ub1nu11/docker/
chmod a+x setenvip.sh
bash setenvip.sh

# Install conteiners
cd ${HOME}
docker-compose -f ub1nu11/docker/docker-compose.yml up -d

### ENV
sleep 5;
echo "Please wait..."

# Install wazuh
curl -sO https://packages.wazuh.com/4.8/wazuh-install.sh
bash wazuh-install.sh -a

### Clean installation:
#cd ..
#rm -rf ub1nu11/

exit 0;
