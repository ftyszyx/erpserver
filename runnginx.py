#! /usr/bin/python2.7
# -*- coding: utf-8 -*-
# vim:expandtab:ts=4:sw=4:
# 
#   ios打包
#
import os
from os.path import join, getsize

nginxpath="G:/mywork/nginx-1.12.2/nginx-1.12.2"
os.chdir( nginxpath )
print("run nginx");
print(os.system("nginx.exe"))