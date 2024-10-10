# set ip
cd ub1nu11/
kur=$(hostname --all-ip-addresses | awk '{print $1}') 
sed -i 's/kurec/'$kur'/g' env/mgmt/index.html
