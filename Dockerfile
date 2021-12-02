FROM debian:buster-slim
WORKDIR /home/akeneo/pim-docs/
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
        python-sphinx && \
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    sh -c 'echo "deb https://packages.sury.org/php/ buster main" > /etc/apt/sources.list.d/php.list' && \
    apt-get update && \
    apt-get install -y --no-install-recommends \
        php8.0-apcu php8.0-bcmath php8.0-cli php8.0-curl php8.0-fpm \
        php8.0-gd php8.0-intl php8.0-mysql php8.0-xml php8.0-zip php8.0-mbstring && \
    echo "memory_limit = 1024M" >> /etc/php/8.0/cli/php.ini && \
    echo "date.timezone = UTC" >> /etc/php/8.0/cli/php.ini && \
    apt-get clean && apt-get --yes --quiet autoremove --purge && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/* && \
    rm -rf /usr/share/locale/* && \
    rm -rf /var/log/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

# Install Akeneo PIM Assets
RUN \
    #
    # Get PIM CE edition
    #
    wget https://github.com/akeneo/pim-community-dev/archive/master.zip -P /home/akeneo/pim-docs/ && \
    unzip /home/akeneo/pim-docs/master.zip -d /home/akeneo/pim-docs/ && \
    cd /home/akeneo/pim-docs/pim-community-dev-master/ && \
    #
    # Install dependencies
    #
    php -d memory_limit=3G /usr/local/bin/composer install --no-suggest --ignore-platform-reqs && \
    cd /home/akeneo/pim-docs/pim-community-dev-master/ && php bin/console pim:installer:assets --env=prod && \
    mkdir /home/akeneo/pim-docs/pim-community-dev-master/public/css && \
    wget http://demo.akeneo.com/css/pim.css -P /home/akeneo/pim-docs/pim-community-dev-master/public/css && \
    #
    # Cleanup
    #
    rm -rf /root/.composer/cache && \
    cd /home/akeneo/pim-docs/pim-community-dev-master/ && ls | grep -v "vendor\|public" | xargs rm -rf
