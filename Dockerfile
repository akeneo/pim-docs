FROM debian:9-slim AS pim
MAINTAINER pierre.allard@akeneo.com
WORKDIR /home/akeneo/pim-docs/

# Sphinx installation
RUN apt-get update && \
    apt-get install -y python-pip git
RUN pip install --upgrade pip
RUN pip install sphinx~=1.5.3 && \
    pip install git+https://github.com/fabpot/sphinx-php.git && \
    pip install git+https://github.com/mickaelandrieu/sphinxcontrib.youtube.git
RUN apt-get clean && apt-get --yes --quiet autoremove --purge && \
    rm -rf /var/lib/apt/lists/*

# Akeneo PIM installation
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    apt-get install -y wget unzip

# Install mysql-server
RUN apt-get install -y lsb-release apt-transport-https ca-certificates && \
    wget -O /tmp/mysql-apt-config.deb https://dev.mysql.com/get/mysql-apt-config_0.8.7-1_all.deb && \
    dpkg -i /tmp/mysql-apt-config.deb && \
    apt-get update && \
    apt-get install -y mysql-server && \
    rm /tmp/mysql-apt-config.deb

# Install php
RUN apt-get install -y apt-transport-https ca-certificates && \
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    sh -c 'echo "deb https://packages.sury.org/php/ stretch main" > /etc/apt/sources.list.d/php.list' && \
    apt update && \
    apt-get install -y php7.1-apcu php7.1-bcmath php7.1-cli php7.1-curl php7.1-fpm php7.1-gd php7.1-intl php7.1-mcrypt php7.1-mysql php7.1-soap php7.1-xml php7.1-zip php7.1-mbstring && \
    echo "memory_limit = 1024M" >> /etc/php/7.1/cli/php.ini && \
    echo "date.timezone = Etc/UTC" >> /etc/php/7.1/cli/php.ini

# Purge apt
RUN apt-get clean && apt-get --yes --quiet autoremove --purge && \
    rm -rf /var/lib/apt/lists/*

# Compute assets
RUN wget https://github.com/akeneo/pim-community-dev/archive/2.1.zip -P /home/akeneo/pim-docs/ && \
    unzip /home/akeneo/pim-docs/2.1.zip -d /home/akeneo/pim-docs/ && \
    wget https://getcomposer.org/download/1.6.2/composer.phar -P /home/akeneo/pim-docs/ && \
    cd /home/akeneo/pim-docs/pim-community-dev-2.1/ && php -d memory_limit=3G ../composer.phar install --no-dev --no-suggest --ignore-platform-reqs && \
    service mysql start && \
    mysql -u root -e "CREATE DATABASE akeneo_pim" && \
    mysql -u root -e "GRANT ALL PRIVILEGES ON akeneo_pim.* TO akeneo_pim@localhost IDENTIFIED BY 'akeneo_pim'" && \
    cd /home/akeneo/pim-docs/pim-community-dev-2.1/ && php bin/console doctrine:schema:create --env=prod && \
    cd /home/akeneo/pim-docs/pim-community-dev-2.1/ && php bin/console pim:installer:assets --env=prod

# Copy script
COPY build.sh /home/akeneo/pim-docs/build.sh
RUN chmod +x /home/akeneo/pim-docs/build.sh
