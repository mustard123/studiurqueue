For docker

Docker version 19.03.6, build 369ce74a3c

docker-compose version 1.25.3, build d4d1b42b

Composer 1.6.3 2018-01-31 16:28:17

Make sure mysql listen on all interfaces:
in mysql.cnf bind = 0.0.0.0

1. In backend: composer require doctrine/orm": "^2.6.2"

2. In backend: ./create_database_.sh

2. In project root: source export_docker_host_ip.sh

3. In project root: docker-compose up