FROM debian:8-slim AS pim
MAINTAINER pierre.allard@akeneo.com
WORKDIR /home/akeneo/pim-docs/

# Sphinx installation
RUN apt-get update && \
    apt-get install -y python-pip git && \
    apt-get clean
RUN pip install --upgrade pip
RUN pip install sphinx~=1.5.3 && \
    pip install git+https://github.com/fabpot/sphinx-php.git && \
    pip install git+https://github.com/mickaelandrieu/sphinxcontrib.youtube.git

# Akeneo PIM installation
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    apt-get install -y wget unzip && \
    apt-get install -y php5 mysql-server apache2 libapache2-mod-php5 php5-cli php5-apcu php5-mcrypt php5-intl php5-mysql php5-curl php5-gd mongodb php5-mongo && \
    apt-get clean && apt-get --yes --quiet autoremove --purge && \
    rm -rf /var/lib/apt/lists/*
RUN echo "memory_limit = 768M" >> /etc/php5/cli/php.ini && \
    echo "date.timezone = Etc/UTC" >> /etc/php5/cli/php.ini

RUN wget https://github.com/akeneo/pim-community-dev/archive/1.6.zip -P /home/akeneo/pim-docs/ && \
    unzip /home/akeneo/pim-docs/1.6.zip -d /home/akeneo/pim-docs/ && \
    wget https://getcomposer.org/download/1.6.2/composer.phar -P /home/akeneo/pim-docs/ && \
    cd /home/akeneo/pim-docs/pim-community-dev-1.6/ && php -d memory_limit=3G ../composer.phar install --no-dev --no-suggest && \
    service mysql start && \
    mysql -u root -e "CREATE DATABASE akeneo_pim" && \
    mysql -u root -e "GRANT ALL PRIVILEGES ON akeneo_pim.* TO akeneo_pim@localhost IDENTIFIED BY 'akeneo_pim'" && \
    cd /home/akeneo/pim-docs/pim-community-dev-1.6/ && php app/console doctrine:schema:create --env=prod && \
    cd /home/akeneo/pim-docs/pim-community-dev-1.6/ && php app/console pim:installer:assets --env=prod

COPY build.sh /home/akeneo/pim-docs/build.sh
RUN chmod +x /home/akeneo/pim-docs/build.sh
