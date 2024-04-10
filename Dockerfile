# Użyj oficjalnego obrazu Ubuntu jako podstawy
FROM ubuntu

# Ustawia informacje o twórcy
LABEL maintainer="Twój Nazwa i Email"

# Aktualizuje repozytoria i instaluje potrzebne narzędzia
RUN apt-get update -y && \
    apt-get install -y \
    curl \
    wget \
    git \
    unzip

# Instaluje PHP i jego zależności
RUN apt-get install -y \
    php \
    php-cli \
    php-mbstring \
    php-xml \
    php-zip \
    php-gd \
    php-mysql \
    php-curl

# Instaluje Composer (menadżer zależności PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Przełącza się do katalogu /var/www, gdzie będzie umieszczony projekt Laravel
WORKDIR /var/www

# Kopiuje plik composer.json i composer.lock do kontenera
COPY composer.json composer.lock /var/www/

# Instaluje zależności PHP za pomocą Composera
RUN composer install --no-scripts --no-autoloader

# Kopiuje resztę projektu do kontenera
COPY . /var/www/

# Generuje autoloader i wykonuje ewentualne skrypty instalacyjne
RUN composer dump-autoload && \
    php artisan key:generate

# Otwiera port 80, na którym będzie działać aplikacja Laravel
EXPOSE 80

# Uruchamia aplikację Laravel
CMD php artisan serve --host=0.0.0.0 --port=80
