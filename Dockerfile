FROM debian:9-slim AS pim
MAINTAINER pierre.allard@akeneo.com
WORKDIR /home/akeneo/pim-docs/
ENV DEBIAN_FRONTEND=noninteractive

# Create environment for build with php, python, mysql and composer
RUN apt-get update && \
    apt-get install -y --no-install-recommends wget lsb-release apt-transport-https ca-certificates gnupg unzip \
        python python-setuptools ssh rsync && \
    #
    # Add source for mysql
    wget -O /tmp/mysql-apt-config.deb https://dev.mysql.com/get/mysql-apt-config_0.8.7-1_all.deb && \
    dpkg -i /tmp/mysql-apt-config.deb && \
    #
    # Add source for php
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    sh -c 'echo "deb https://packages.sury.org/php/ stretch main" > /etc/apt/sources.list.d/php.list' && \
    #
    # Add sphinx
    wget -O /tmp/sphinx.zip https://github.com/sphinx-doc/sphinx/archive/v1.5.6.zip && \
    unzip /tmp/sphinx.zip -d /tmp/ && \
    cd /tmp/sphinx-1.5.6/ && \
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
    wget https://github.com/akeneo/pim-community-dev/archive/1.7.zip -P /home/akeneo/pim-docs/ && \
    unzip /home/akeneo/pim-docs/1.7.zip -d /home/akeneo/pim-docs/ && \
    wget https://getcomposer.org/download/1.6.2/composer.phar -P /home/akeneo/pim-docs/ && \
    cd /home/akeneo/pim-docs/pim-community-dev-1.7/ && php -d memory_limit=3G ../composer.phar install --no-dev --no-suggest --ignore-platform-reqs && \
    service mysql start && \
    mysql -u root -e "CREATE DATABASE akeneo_pim" && \
    mysql -u root -e "GRANT ALL PRIVILEGES ON akeneo_pim.* TO akeneo_pim@localhost IDENTIFIED BY 'akeneo_pim'" && \
    cd /home/akeneo/pim-docs/pim-community-dev-1.7/ && php app/console doctrine:schema:create --env=prod && \
    cd /home/akeneo/pim-docs/pim-community-dev-1.7/ && php app/console pim:installer:assets --env=prod && \
    service mysql stop && \
    #
    # Clean
    rm -rf /root/.composer/cache && \
    cd /home/akeneo/pim-docs/pim-community-dev-1.7/ && ls | grep -v "vendor\|web" | xargs rm -rf && \
    rm /var/lib/mysql/ibdata1 /var/lib/mysql/ib_logfile1 /var/lib/mysql/ib_logfile0
