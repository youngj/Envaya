#!/bin/bash

# configures dropbox on ubuntu as a simple backup mechanism

groupadd dropbox
useradd -r -d /etc/dropbox -g dropbox -s /bin/false dropbox
wget -O /tmp/dropbox.tar.gz http://www.dropbox.com/download/?plat=lnx.x86
mkdir -p /usr/local/dropbox /etc/dropbox
chown dropbox.dropbox /etc/dropbox
chmod 755 /etc/dropbox

tar xvzf /tmp/dropbox.tar.gz -C /usr/local/dropbox --strip 1
rm /tmp/dropbox.tar.gz

cat <<EOF | mysql

CREATE USER 'dropbox'@'localhost' IDENTIFIED BY '';
GRANT SELECT, LOCK TABLES ON envaya.* TO 'dropbox'@'localhost';

FLUSH PRIVILEGES;

EOF

cat <<EOF | sed -e "s,%,$,g" >/etc/init.d/dropbox
# dropbox service
DROPBOX_USERS="dropbox"
DAEMON=/usr/local/dropbox/dropbox
unset DISPLAY

start() {
    echo "Starting dropbox..."
    for dbuser in %DROPBOX_USERS; do
        HOMEDIR=%(getent passwd %dbuser | cut -d: -f6)
        if [ -x %DAEMON ]; then
            HOME="%HOMEDIR" start-stop-daemon -b -o -c %dbuser -S -u %dbuser -x %DAEMON
        fi
    done
}

stop() {
    echo "Stopping dropbox..."
    for dbuser in %DROPBOX_USERS; do
        HOMEDIR=%(getent passwd %dbuser | cut -d: -f6)
        if [ -x %DAEMON ]; then
            start-stop-daemon -o -c %dbuser -K -u %dbuser -x %DAEMON
        fi
    done
}

status() {
    for dbuser in %DROPBOX_USERS; do
        dbpid=%(pgrep -u %dbuser dropbox)
        if [ -z %dbpid ] ; then
            echo "dropboxd for USER %dbuser: not running."
        else
            echo "dropboxd for USER %dbuser: running (pid %dbpid)"
        fi
    done
}


case "%1" in
  start)
    start
    sleep 1
    status
    ;;

  stop)
    stop
    sleep 1
    status
    ;;

  restart|reload|force-reload)
    stop
    start
    sleep 1
    status
    ;;

  status)
    status
    ;;

  *)
    echo "Usage: /etc/init.d/dropbox {start|stop|reload|force-reload|restart|status}"
    exit 1

esac

exit 0
EOF

chmod a+x /etc/init.d/dropbox
update-rc.d dropbox defaults

/etc/init.d/dropbox start

sleep 4

cat <<EOF > /etc/dropbox/dropboxp2p.py
#!/usr/bin/python2.5

# Script for enabling/disabling P2P broadcasts.
#
# Execute without flags to show current setting.
# Execute with -e or -d to en- or disable broadcasts, respectively.
#
# Written by Peter Schulz (dropbox-ps@trashmail.net).

import optparse
import os
import sqlite3

# parse command line options
cmdparser = optparse.OptionParser()
cmdparser.add_option("-e", action="store_true", dest="enable", help="enable P2P")
cmdparser.add_option("-d", action="store_true", dest="disable", help="disable P2P")
(options, args) = cmdparser.parse_args()

db = sqlite3.connect('/etc/dropbox/.dropbox/dropbox.db')
cursor = db.cursor()
key = "p2p_enabled"

value_d = "STAwCi4=" # base64 of "I00\n."
value_e = "STAxCi4=" # base64 of "I01\n."

enabled = False
cursor.execute("select value from config where key=?", (key, ))
for entry in cursor:
   enabled = entry[0] == value_e

print "P2P currently is %s" % ('enabled' if enabled else 'disabled')

if options.enable or options.disable:
   # set p2p_enabled
   value = (value_e if options.enable else value_d)
   cursor.execute("delete from config where key=?", (key, ))
   cursor.execute("insert into config (key, value) values (?, ?)", (key, value))
   print "P2P now is %s" % ('enabled' if options.enable else 'disabled')

db.commit();
db.close();
EOF
python /etc/dropbox/dropboxp2p.py -d

cat <<EOF > /etc/dropbox/get_host_id.py
#!/usr/bin/python
import base64, pickle, sqlite3, os, string

dropbox_db_path = '/etc/dropbox/.dropbox/dropbox.db'

db = sqlite3.connect(dropbox_db_path)
cur = db.cursor()
cur.execute('select key, value from config where key ="host_id" order by key')
for row in cur: 
    if row[1] != None:
        print "Visit the following link in your browser to register the host with dropbox:"
        print 'https://www.dropbox.com/cli_link?host_id=' + string.lstrip(pickle.loads(base64.b64decode(row[1])))
db.close()
EOF
python /etc/dropbox/get_host_id.py

/etc/init.d/dropbox restart