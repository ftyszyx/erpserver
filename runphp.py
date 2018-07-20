#! /usr/bin/python2.7
# -*- coding: utf-8 -*-
# vim:expandtab:ts=4:sw=4:
# 
#   ios打包
#
import os
from os.path import join, getsize

phpcgi="C:/wamp/bin/php/php5.5.12/php-cgi.exe"
phpini="C:/wamp/bin/php/php5.5.12/php.ini"

cmd = phpcgi+" -b 127.0.0.1:9000 -c "+phpini
print("run php");
print(os.system( cmd )) 