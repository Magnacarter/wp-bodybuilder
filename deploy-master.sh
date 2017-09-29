#!/bin/bash

HOST=HOST
USER=USERNAME
PASS=PASS

cd ../

sudo apt-get install ncftp

ncftp -u $USER -p $PASS $HOST << EOF

cd public_html/plugin-testing/wp-content/plugins

rm -rf wp-bodybuilder

put -R wp-bodybuilder

# End FTP Connection
exit

EOF

