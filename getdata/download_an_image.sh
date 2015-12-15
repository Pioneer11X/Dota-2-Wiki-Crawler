#!/bin/sh

if [ $# -eq 0 ]
then
	echo "Usage: $0 <File Url>"
else
	cd /var/www/sravan/appdata/resources/images/
	wget $1
fi
