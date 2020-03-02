#!/usr/bin/env bash
set -e
PATH_SELF=`realpath $(dirname "${BASH_SOURCE[0]}")`

red=$'\e[1;31m'
grn=$'\e[1;32m'
yel=$'\e[1;33m'
blu=$'\e[1;34m'
mag=$'\e[1;35m'
cyn=$'\e[1;36m'
end=$'\e[0m'


DATABASE=$(grep ^DATABASE .env | cut -d '=' -f2- | cut -d '"' -f2)
DB_USERNAME=$(grep ^DB_USERNAME .env | cut -d '=' -f2- | cut -d '"' -f2)
DB_PASSWORD=$(grep ^DB_PASSWORD .env | cut -d '=' -f2- | cut -d '"' -f2)

TEST_DATABASE=$(grep ^TEST_DATABASE .env | cut -d '=' -f2- | cut -d '"' -f2)


if [[ -z $DATABASE || -z $DB_USERNAME || -z $DB_PASSWORD || -z TEST_DATABASE || -z TEST_DB_HOST || -z TEST_DB_USERNAME || -z TEST_DB_PASSWORD ]]; then
  echo "${red}one or more variables are undefined${end}"
  exit 1
fi
echo "Creating database if not exists ${grn}${DATABASE}${end} ..."
echo "create database if not exists ${DATABASE}" | sudo mysql 2> >(sed $'s,.*,\e[31m&\e[m,')
echo "Creating user if not exists ${grn}${DB_USERNAME}${end} ..."
echo "create user if not exists ${DB_USERNAME}@'%' identified by '${DB_PASSWORD}' " | sudo mysql 2> >(sed $'s,.*,\e[31m&\e[m,')
echo "Granting all privileges on ${grn}${DATABASE}.* ${end}to user ${grn}${DB_USERNAME}${end} ..."
echo "grant all privileges on ${DATABASE}.* to ${DB_USERNAME}@'%'" | sudo mysql 2> >(sed $'s,.*,\e[31m&\e[m,')

echo "Creating test database if not exists ${grn}${TEST_DATABASE}${end} ..."
echo "create database if not exists ${TEST_DATABASE}" | sudo mysql 2> >(sed $'s,.*,\e[31m&\e[m,')
echo "Granting all privileges on ${grn}${TEST_DATABASE}.* ${end}to user ${grn}${DB_USERNAME}${end} ..."
echo "grant all privileges on ${TEST_DATABASE}.* to ${DB_USERNAME}@'%'" | sudo mysql 2> >(sed $'s,.*,\e[31m&\e[m,')
echo ""
echo "Creating tables ..."

php vendor/bin/doctrine orm:schema-tool:update --force -n 2> >(sed $'s,.*,\e[31m&\e[m,')

echo "Set up mysql scheduler to close all tickets every day at 1 ."
echo "use studiurQueue; SET GLOBAL event_scheduler = ON; CREATE EVENT IF NOT EXISTS close_tickets ON SCHEDULE EVERY 1 DAY_HOUR DO UPDATE ticket SET status='closed';" |  sudo mysql 2> >(sed $'s,.*,\e[31m&\e[m,')


