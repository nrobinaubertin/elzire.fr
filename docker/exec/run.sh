#!/bin/sh

echo "%$GROUP ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers
adduser -h "/exec" -D -u "$UID" "$USER"
grep -q "$GROUP" /etc/group || addgroup -g "$GID" "$GROUP"
addgroup "$USER" "$GROUP"
echo "$USER:plop" | chpasswd

su-exec "$UID:$GID" "$@"
