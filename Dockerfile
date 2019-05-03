FROM mysql:5.7
MAINTAINER pierre.allard@akeneo.com
WORKDIR /home/akeneo/pim-docs/
ENV DEBIAN_FRONTEND=noninteractive

# Create environment for build with php, python, mysql and composer
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y --no-install-recommends wget lsb-release apt-transport-https ca-certificates gnupg unzip \
        python python-setuptools ssh rsync && \
    #
    # Add source for php
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    sh -c 'echo "deb https://packages.sury.org/php/ stretch main" > /etc/apt/sources.list.d/php.list' && \
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
    apt-get install -y --no-install-recommends php7.1-apcu php7.1-bcmath \
        php7.1-cli php7.1-curl php7.1-fpm php7.1-gd php7.1-intl php7.1-mcrypt php7.1-mysql php7.1-soap php7.1-xml \
        php7.1-zip php7.1-mbstring && \
    #
    # Configure PHP
    echo "memory_limit = 1024M" >> /etc/php/7.1/cli/php.ini && \
    echo "date.timezone = Etc/UTC" >> /etc/php/7.1/cli/php.ini && \
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
    wget https://github.com/akeneo/pim-community-dev/archive/v2.2.12.zip -O /home/akeneo/pim-docs/2.2.zip && \
    unzip /home/akeneo/pim-docs/2.2.zip -d /home/akeneo/pim-docs/ && \
    mv /home/akeneo/pim-docs/pim-community-dev-2.2.12 /home/akeneo/pim-docs/pim-community-dev-2.2
    #
    # Install Akeneo PIM
RUN cd /home/akeneo/pim-docs/pim-community-dev-2.2/ && \
    php -d memory_limit=3G /home/akeneo/pim-docs/composer.phar install --no-dev --no-suggest --prefer-dist --no-scripts

COPY docker/wait_for_mysql.sh /wait_for_mysql.sh
RUN service mysql start && chmod +x /wait_for_mysql.sh && /wait_for_mysql.sh && \
    mysql -u root -e "CREATE DATABASE akeneo_pim" && \
    mysql -u root -e "GRANT ALL PRIVILEGES ON akeneo_pim.* TO akeneo_pim@localhost IDENTIFIED BY 'akeneo_pim'" && \
    cp /home/akeneo/pim-docs/pim-community-dev-2.2/app/config/parameters.yml.dist /home/akeneo/pim-docs/pim-community-dev-2.2/app/config/parameters.yml && \
    cd /home/akeneo/pim-docs/pim-community-dev-2.2/ && php bin/console doctrine:schema:create --env=prod && \
    cd /home/akeneo/pim-docs/pim-community-dev-2.2/ && php bin/console pim:installer:assets --env=prod
    #
    # Clean
RUN rm -rf /root/.composer/cache && \
    cd /home/akeneo/pim-docs/pim-community-dev-2.2/ && ls | grep -v "vendor\|web" | xargs rm -rf
