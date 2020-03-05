# For docker:

## Requirements
* Docker version 19.03.6, build 369ce74a3c
* docker-compose version 1.25.3, build d4d1b42b
* composer and php 7.2 or greater on local system 
* mysql installed on local system

Make sure mysql listen on all interfaces:
in mysql.cnf set find entry bind and set ```bind = 0.0.0.0``` 
then restart mysql

### Setup and run with docker

* In backend directory in .env file set: ```DB_HOST="host.docker.internal"```
 
* In backend directory run command: ```composer require doctrine/orm:^2.6.2 --ignore-platform-reqs```

* In backend directory run: ```./create_database_.sh```

* In project root directory run command: ```source export_docker_host_ip.sh```

* In project root directory run command: ```docker-compose up```

By default the container exposes two ports
8888 for the web interface: http://localhost:8888
7777 for the socket server: ws://localhost:7777