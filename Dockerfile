FROM debian:buster-slim
ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
    apt-get install -y --no-install-recommends wget lsb-release ca-certificates gnupg unzip \
        python ssh rsync curl \
        python-jinja2 \
        python-sphinx \
        php7.3-apcu php7.3-bcmath php7.3-cli php7.3-curl php7.3-fpm php7.3-gd php7.3-intl php7.3-mysql php7.3-xml php7.3-zip php7.3-mbstring php7.3-imagick && \
    echo "memory_limit = 1024M" >> /etc/php/7.3/cli/php.ini && \
    echo "date.timezone = UTC" >> /etc/php/7.3/cli/php.ini && \
    apt-get clean && apt-get --yes --quiet autoremove --purge && \
    wget https://getcomposer.org/download/1.10.1/composer.phar -P /usr/local/bin/ && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/* && \
    rm -rf /usr/share/doc/* && \
    rm -rf /usr/share/man/* && \
    rm -rf /usr/share/locale/* && \
    rm -rf /var/log/*

RUN useradd -m akeneo && \
    mkdir /home/akeneo/.composer && \
    chown -R akeneo /home/akeneo/

WORKDIR /home/akeneo/
VOLUME /home/akeneo/.composer
USER akeneo
