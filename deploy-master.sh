#!/bin/bash
FTP_USER=adam@adamkristopher.com
FTP_PASSWORD=Wingchun78

find wp-bodybuilder -type f -exec curl -u $FTP_USER:$FTP_PASSWORD --ftp-create-dirs -T {} ftp://107.180.50.244/plugin-testing/wp-content/plugins/wp-bodybuilder/{} \;