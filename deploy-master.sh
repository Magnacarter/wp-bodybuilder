#!/bin/bash

HOST=107.180.50.244
USER=adamkcarter
PASS=Wingchun78

spawn ssh $USER@$HOST
expect "password:"
sleep 1
send $PASS

cd /public_html/plugin-testing/wp-content/plugins

rm -rf wp-bodybuilder

put -R wp-bodybuilder

