#!/bin/sh
echo "*/1	*	*	*	*	run-parts /etc/periodic/1min" > /etc/crontabs/root
crontab -l
# start cron
crond -f -l 8