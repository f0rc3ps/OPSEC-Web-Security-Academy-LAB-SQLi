#!/bin/sh
echo "Give username"
read user
echo "Give password"
read pass
echo "Give storesec_file"
read store
# Protect 
htpasswd -bm -c $store $user $pass
  exit 0;
