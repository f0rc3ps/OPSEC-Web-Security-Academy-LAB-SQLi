#!/usr/bin/bash
# @nu11secur1ty

echo "Give the new user"
	read user
htpasswd /etc/apache2/secret $user
	  exit 0
