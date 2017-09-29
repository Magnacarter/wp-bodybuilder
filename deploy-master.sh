#!/bin/bash

cd ../

sudo apt-get install ncftp

ncftp -u USERNAME -p PASSWORD HOST << EOF

cd public_html/plugin-testing/wp-content/plugins

rm -rf wp-bodybuilder

put -R wp-bodybuilder

# End FTP Connection
exit

EOF

