#!/bin/bash

# Cambiar al directorio del proyecto Docker
cd /home/ubuntu/docker-lamp || { echo "Fallo al cambiar al directorio /home/ubuntu/docker-lamp"; exit 1; }

# Ejecuta el contenedor si no está en funcionamiento
docker-compose up -d

# Espera unos segundos para asegurarse de que el contenedor está completamente arrancado
sleep 10

# Ejecutar comandos dentro del contenedor
docker exec -it docker-lamp_www_1 bash -c "
    apt-get update && \
    apt-get install -y software-properties-common && \
    apt-get install -y certbot python3-certbot-apache
"

# Ejecutar Certbot para obtener certificados SSL y redirigir HTTP a HTTPS automáticamente
docker exec -it docker-lamp_www_1 certbot --apache --agree-tos --no-eff-email --email 11snaider99@gmail.com -d hoteleasewebserver1.myddns.me --redirect --non-interactive
