#!/usr/bin/env bash

#set env for docker compose
export DOCKER_HOST_IP=$(ip route | grep docker | awk '{print $9}';)