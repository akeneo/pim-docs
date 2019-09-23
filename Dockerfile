FROM debian:buster
MAINTAINER pierre.allard@akeneo.com
WORKDIR /home/akeneo/pim-docs/
ENV DEBIAN_FRONTEND=noninteractive

# Create environment for build with php, python, mysql and composer
RUN apt-get update && \
    apt-get install -y --no-install-recommends wget lsb-release apt-transport-https ca-certificates gnupg unzip \
        python python-setuptools ssh rsync curl software-properties-common && \
    #
    # Add sphinx
    wget -O /tmp/sphinx.zip https://github.com/sphinx-doc/sphinx/archive/v1.8.4.zip && \
    unzip /tmp/sphinx.zip -d /tmp/ && \
    cd /tmp/sphinx-1.8.4/ && \
    python setup.py install && \
    #
    # Add youtube-sphinx extension
    wget -O /tmp/sphinxcontrib.youtube.zip \
        https://github.com/mickaelandrieu/sphinxcontrib.youtube/archive/master.zip && \
    unzip /tmp/sphinxcontrib.youtube.zip -d /tmp/ && \
    cd /tmp/sphinxcontrib.youtube-master/ && \
    python setup.py install && \
    #
    # Add sphinx-php extension
    wget -O /tmp/sphinx-php.zip \
        https://github.com/fabpot/sphinx-php/archive/v1.0.10.zip && \
    unzip /tmp/sphinx-php.zip -d /tmp/ && \
    cd /tmp/sphinx-php-1.0.10/ && \
    python setup.py install && \
    #
    # Download packages
    apt-get update && \
    apt-get install -y --no-install-recommends php7.3-apcu php7.3-bcmath php7.3-cli php7.3-curl php7.3-fpm php7.3-gd php7.3-intl php7.3-mysql php7.3-xml php7.3-zip php7.3-mbstring nodejs yarnpkg && \
    #
    # Configure PHP
    echo "memory_limit = 1024M" >> /etc/php/7.3/cli/php.ini && \
    echo "date.timezone = UTC" >> /etc/php/7.3/cli/php.ini && \
    #
    # Get composer
    wget https://getcomposer.org/download/1.6.2/composer.phar -P /home/akeneo/pim-docs/ && \
    #
    # Clean
    apt-get clean && apt-get --yes --quiet autoremove --purge && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/* && \
    rm -rf /usr/share/doc/* && \
    rm -rf /usr/share/man/* && \
    rm -rf /usr/share/locale/* && \
    rm -rf /var/log/*

COPY build.sh /home/akeneo/pim-docs/build.sh

# Install Akeneo PIM Assets
RUN chmod +x /home/akeneo/pim-docs/build.sh && \
    #
    # Download curent version
    wget https://github.com/akeneo/pim-community-dev/archive/master.zip -P /home/akeneo/pim-docs/ && \
    unzip /home/akeneo/pim-docs/master.zip -d /home/akeneo/pim-docs/ && \
    #
    # Install vendors
    cd /home/akeneo/pim-docs/pim-community-dev-master/ && \
    php -d memory_limit=3G /home/akeneo/pim-docs/composer.phar install --no-dev --no-suggest --ignore-platform-reqs

RUN cd /home/akeneo/pim-docs/pim-community-dev-master/ && php bin/console pim:installer:assets --env=prod && \
    cd /home/akeneo/pim-docs/pim-community-dev-master/ && sed -i "s#replace: '/bundles'#replace: '../bundles'#" frontend/build/compile-less.js && \
    cd /home/akeneo/pim-docs/pim-community-dev-master/ && yarnpkg install && yarnpkg less
    #
    # Clean
RUN rm -rf /root/.composer/cache && \
    cd /home/akeneo/pim-docs/pim-community-dev-master/ && ls | grep -v "vendor\|web" | xargs rm -rf