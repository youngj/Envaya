#!/bin/bash
SETUP_DIR=$(cd `dirname $0` && pwd)
SCRIPT_DIR=`dirname $SETUP_DIR`
INSTALL_DIR=`dirname $SCRIPT_DIR`

apt-get -y install default-jre-headless

mkdir -p /var/kestrel
chmod 755 /var/kestrel

groupadd kestrel
useradd -r -d /var/kestrel -g kestrel -s /bin/false kestrel
chown kestrel.kestrel /var/kestrel

cp $INSTALL_DIR/vendors/kestrel_dev/kestrel-1.2.jar /var/kestrel
cp -r $INSTALL_DIR/vendors/kestrel_dev/libs /var/kestrel

cat <<EOF > /var/kestrel/production.conf

port = 22133
host = "127.0.0.1"

log {
  filename = "/var/kestrel/kestrel.log"
  roll = "daily"
  level = "info"
}

queue_path = "/var/kestrel/kestrel.queue"
timeout = 0
max_journal_size = 16277216
max_memory_size = 134217728
max_journal_overflow = 10

EOF

cp $SETUP_DIR/init.d/kestrel /etc/init.d/kestrel
chmod 755 /etc/init.d/kestrel
update-rc.d kestrel defaults 95
/etc/init.d/kestrel start
