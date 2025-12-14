FROM php:8.2-apache

# Installa l'estensione mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Attiva il mod_rewrite
RUN a2enmod rewrite

# --- AGGIUNTA FONDAMENTALE PER I FILE GRANDI ---
# Creiamo un file di configurazione per aumentare i limiti a 100MB
RUN echo "file_uploads = On\n" \
         "memory_limit = 256M\n" \
         "upload_max_filesize = 100M\n" \
         "post_max_size = 100M\n" \
         "max_execution_time = 600\n" \
         > /usr/local/etc/php/conf.d/uploads.ini