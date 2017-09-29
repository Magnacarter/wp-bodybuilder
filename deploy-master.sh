#!/bin/bash

HOST=107.180.50.244
USER=adamkcarter
PASS=Wingchun78

ssh $USER@$HOST

cd /public_html/plugin-testing/wp-content/plugins

rm -rf wp-bodybuilder

put -R wp-bodybuilder

