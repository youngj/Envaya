#!/bin/bash
#
# installing these packages can take a long time, so wait to do these until later
#

SETUP_DIR=$(cd `dirname $0` && pwd)

sed -i -e 's/^#PS1=/PS1=/' /root/.bashrc # enable the colorful root bash prompt

apt-get -y install emacs mcrypt
apt-get -y install poppler-utils netpbm cups-pdf 

cp $SETUP_DIR/conf/cups-pdf.conf /etc/cups/

apt-get -y install openoffice.org-writer openoffice.org-draw
