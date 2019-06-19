#!/bin/bash
cd webroot
while :
do
php cron.php >> ../cron.log
sleep 1h
done
