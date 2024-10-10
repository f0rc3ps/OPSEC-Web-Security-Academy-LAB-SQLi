#!/usr/bin/bash

echo "### Now stopping ${HOME}/ub1nu11."
docker stop ub1nu11
docker system prune -a
apt autoremove -y

# Clean wazuh 
rm -rf /etc/apt/sources.list.d/wazuh-install.sh

# Clean docker
for pkg in docker.io docker-doc docker-compose docker-compose-v2 podman-docker containerd runc; do sudo apt-get remove $pkg; done
sleep 3;
systemctl stop docker.socket
sleep 5;
systemctl stop docker.service
sleep 5;
sudo rm -rf /var/lib/docker 
sudo rm -rf /etc/docker
cd /etc/apt/sources.list.d/
rm -rf *docker*
apt autoremove -y
apt update && apt upgrade -y

echo "### Now removing ${HOME}/ub1nu11."
sudo rm -rf ${HOME}/ub1nu11
apt autoremove -y

### Uninstall Wazuh
curl -sO https://packages.wazuh.com/4.8/wazuh-install.sh
bash wazuh-install.sh --uninstall
apt-get remove --purge wazuh-dashboard -y
apt-get remove --purge wazuh-manager -y
systemctl disable wazuh-manager
systemctl daemon-reload
apt-get remove --purge filebeat -y
apt-get remove --purge wazuh-indexer -y
rm -rf /etc/apt/sources.list.d/*wazuh-*

exit 0;
