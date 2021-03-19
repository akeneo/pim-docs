FROM debian:buster-slim
ENV DEBIAN_FRONTEND=noninteractive

RUN echo 'APT::Install-Recommends "0" ; APT::Install-Suggests "0" ;' > /etc/apt/apt.conf.d/01-no-recommended && \
    echo 'path-exclude=/usr/share/man/*' > /etc/dpkg/dpkg.cfg.d/path_exclusions && \
    echo 'path-exclude=/usr/share/doc/*' >> /etc/dpkg/dpkg.cfg.d/path_exclusions && \
    apt-get update && \
    apt-get install -y \
        wget \
        lsb-release \
        ca-certificates \
        gnupg \
        unzip \
        python ssh rsync curl \
        python-jinja2 \
        python-sphinx \
        git \
        php7.3-apcu \
        php7.3-bcmath \
        php7.3-cli \
        php7.3-curl \
        php7.3-fpm \
        php7.3-gd \
        php7.3-intl \
        php7.3-mysql \
        php7.3-xml \
        php7.3-zip \
        php7.3-mbstring \
        php7.3-imagick && \
    echo "memory_limit = 1024M" >> /etc/php/7.3/cli/php.ini && \
    echo "date.timezone = UTC" >> /etc/php/7.3/cli/php.ini && \
    apt-get clean && apt-get --yes --quiet autoremove --purge && \
    apt-get clean && rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/* && \
    rm -rf /usr/share/locale/* && \
    rm -rf /var/log/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

RUN useradd -m akeneo

WORKDIR /home/akeneo/
USER akeneo
