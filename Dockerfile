FROM php:8.0-apache

# Installa le estensioni per connettersi a MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Abilita il mod_rewrite di Apache (utile per il futuro)
RUN a2enmod rewrite

# Copia il codice sorgente dentro il container
COPY . .

# Dai i permessi alla cartella uploads affinché PHP possa scriverci dentro
RUN chown -R www-data:www-data ./uploads