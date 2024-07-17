FROM php:8.0.0-apache
ARG DEBIAN_FRONTEND=noninteractive

RUN docker-php-ext-install mysqli

# instalar PDO y PDO MySQL
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql

RUN apt-get update \
    && apt-get install -y sendmail libpng-dev \
    && apt-get install -y libzip-dev \
    && apt-get install -y zlib1g-dev \
    && apt-get install -y libonig-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip

RUN docker-php-ext-install mbstring
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd

RUN a2enmod rewrite

# Configurar Apache para permitir la visualización de directorios
RUN sed -i '/<Directory \/var\/www\/html\/>/a\    Options +Indexes\n    AllowOverride All\n    Require all granted' /etc/apache2/apache2.conf

# Configurar DirectoryIndex
RUN sed -i 's/DirectoryIndex index.html index.cgi index.pl index.php/DirectoryIndex index.php index.html/' /etc/apache2/apache2.conf

# Configurar CORS para el servicio administrador
RUN echo '<IfModule mod_headers.c>\n    SetEnvIf Origin "http://localhost(:\d+)?$" AccessControlAllowOrigin=$0\n    Header add Access-Control-Allow-Origin %{AccessControlAllowOrigin}e env=AccessControlAllowOrigin\n    Header set Access-Control-Allow-Methods: "*"\n</IfModule>' >> /etc/apache2/apache2.conf

