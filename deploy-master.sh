#!/bin/bash

HOST=107.180.50.244
USER=adam@adamkristopher.com
PASS=Wingchun78

cd ../

sudo apt-get install ncftp

ncftp -u $USER -p $PASS $HOST << EOF

cd /www/wp-content/plugins

rm -rf wp-bodybuilder

put -R wp-bodybuilder

# End FTP Connection
exit

EOF

