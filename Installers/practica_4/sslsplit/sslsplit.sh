sslsplit -D -l connections.log -j /tmp/sslsplit/ -S /tmp/sslsplit -k ca.key -c ca.crt ssl 0.0.0.0 8443 tcp 0.0.0.0 8080 
