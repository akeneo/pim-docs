FROM debian:buster-slim
WORKDIR /home/akeneo/pim-docs/
ENV DEBIAN_FRONTEND=noninteractive
ARG PHP_VERSION

RUN echo 'APT::Install-Suggests "0" ;' > /etc/apt/apt.conf.d/01-no-recommended && \
    echo 'path-exclude=/usr/share/man/*' > /etc/dpkg/dpkg.cfg.d/path_exclusions && \
    echo 'path-exclude=/usr/share/doc/*' >> /etc/dpkg/dpkg.cfg.d/path_exclusions && \
    apt-get update && \
    apt-get install -y \
    wget \
    lsb-release \
    ca-certificates \
    gnupg \
    unzip \
    git \
    ssh \
    rsync \
    curl \
    python \
    python-jinja2 \
    python-pip && \
    pip install sphinx git+https://github.com/fabpot/sphinx-php.git git+https://github.com/mickaelandrieu/sphinxcontrib.youtube.git && \
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    sh -c 'echo "deb https://packages.sury.org/php/ buster main" > /etc/apt/sources.list.d/php.list' && \
    apt-get update && \
    apt-get install -y --no-install-recommends \
    php${PHP_VERSION}-apcu php${PHP_VERSION}-bcmath php${PHP_VERSION}-cli php${PHP_VERSION}-curl php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-gd php${PHP_VERSION}-intl php${PHP_VERSION}-mysql php${PHP_VERSION}-xml php${PHP_VERSION}-zip php${PHP_VERSION}-mbstring && \
    echo "memory_limit = 1024M" >> /etc/php/${PHP_VERSION}/cli/php.ini && \
    echo "date.timezone = UTC" >> /etc/php/${PHP_VERSION}/cli/php.ini && \
    apt-get clean && apt-get --yes --quiet autoremove --purge && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/* && \
    rm -rf /usr/share/locale/* && \
    rm -rf /var/log/*

COPY --from=composer:1 /usr/bin/composer /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

ARG PIM_VERSION
# Install Akeneo PIM Assets
RUN \
    #
    # Get PIM CE edition
    #
    wget https://github.com/akeneo/pim-community-dev/archive/${PIM_VERSION}.zip -P /home/akeneo/pim-docs/ && \
    unzip /home/akeneo/pim-docs/${PIM_VERSION}.zip -d /home/akeneo/pim-docs/ && \
    #
    # Install dependencies
    #
    cd /home/akeneo/pim-docs/pim-community-dev-${PIM_VERSION}/ && \
    php -d memory_limit=3G /usr/local/bin/composer install --no-dev --no-suggest --ignore-platform-reqs && \
    php bin/console pim:installer:assets --env=prod && \
    mkdir -p /home/akeneo/pim-docs/pim-community-dev-${PIM_VERSION}/public/css && \
    wget http://demo.akeneo.com/css/pim.css -P /home/akeneo/pim-docs/pim-community-dev-${PIM_VERSION}/public/css && \
    #
    # Cleanup
    #
    rm -rf /root/.composer/cache && \
    ls | grep -v "vendor\|public" | xargs rm -rf
