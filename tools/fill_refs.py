#!/usr/bin/python
import os, os.path
import smtplib

fnames = ['fill_gibdd_reference', 'fill_prosecutor_reference']
host = 'dev.rosyama.ru'

def isempty(fname):
  if os.path.getsize(fname) == 0:
    return " EMPTY"
  else:
    return ""

def send_mail(fname):
  fromaddr = "root@"+host
  toaddrs = ["fezeev@gmail.com"]
  
  msg = ("From: %s\r\nTo: %s\r\n" % (fromaddr, ", ".join(toaddrs)))
  msg = msg + "Subject: script "+fname+" run result is"+isempty(fname)+":\r\n\r\n"
  
  f = open(fname)
  msg = msg + f.read()
  f.close()

  server = smtplib.SMTP('localhost')
  server.set_debuglevel(1)
  server.sendmail(fromaddr, toaddrs, msg)
  server.quit()
  

for fname in fnames:
  if os.path.exists(fname):
    os.remove(fname)
  os.system('wget http://'+host+'/sprav/'+fname)
  if os.path.exists(fname):
    send_mail(fname)
                                                   
