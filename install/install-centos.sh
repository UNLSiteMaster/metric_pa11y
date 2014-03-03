yum install npm --enablerepo=epel

#prevent Error: SELF_SIGNED_CERT_IN_CHAIN
npm config set ca null

npm install -g phantomjs
npm install -g pa11y
