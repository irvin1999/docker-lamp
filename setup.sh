#!/bin/bash

# Ejecuta los comandos necesarios dentro del contenedor
apt-get update
apt-get install -y software-properties-common
apt-get install -y certbot python3-certbot-apache

# Salir del contenedor
exit
