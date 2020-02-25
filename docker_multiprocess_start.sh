#!/bin/bash

# Start the first process
echo "start apache"
apache2-foreground &
status=$?
if [ $status -ne 0 ]; then
  echo "Failed to start my_first_process: $status"
  exit $status
fi

# Start the second process
echo "start websocket" &
php /var/www/html/backend/ratchet/bin/studiur-queue-server.php
status=$?
if [ $status -ne 0 ]; then
  echo "Failed to start my_second_process: $status"
  exit $status
fi

