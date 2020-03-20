FROM debian:buster-slim
WORKDIR /home/akeneo/pim-docs/
ENV DEBIAN_FRONTEND=noninteractive

# Create environment for build with php, python, mysql and composer
RUN apt-get update && \
    apt-get install -y --no-install-recommends wget lsb-release apt-transport-https ca-certificates gnupg unzip \
        python python-setuptools ssh rsync curl software-properties-common \
        python-jinja2 \
        python-sphinx \
        python-pip \
        php7.3-apcu php7.3-bcmath php7.3-cli php7.3-curl php7.3-fpm php7.3-gd php7.3-intl php7.3-mysql php7.3-xml php7.3-zip php7.3-mbstring && \
    #
    # Add youtube-sphinx extension
    #wget -O /tmp/sphinxcontrib.youtube.zip \
    #    https://github.com/mickaelandrieu/sphinxcontrib.youtube/archive/master.zip && \
    #unzip /tmp/sphinxcontrib.youtube.zip -d /tmp/ && \
    #cd /tmp/sphinxcontrib.youtube-master/ && \
    #python setup.py install && \
    #
    # Add sphinx-php extension
    echo "memory_limit = 1024M" >> /etc/php/7.3/cli/php.ini && \
    echo "date.timezone = UTC" >> /etc/php/7.3/cli/php.ini && \
    wget https://getcomposer.org/download/1.10.1/composer.phar -P /home/akeneo/pim-docs/ && \
    apt-get clean && apt-get --yes --quiet autoremove --purge && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/* && \
    rm -rf /usr/share/doc/* && \
    rm -rf /usr/share/man/* && \
    rm -rf /usr/share/locale/* && \
    rm -rf /var/log/*


# Install Akeneo PIM Assets
RUN wget https://github.com/akeneo/pim-community-dev/archive/master.zip -P /home/akeneo/pim-docs/ && \
    unzip /home/akeneo/pim-docs/master.zip -d /home/akeneo/pim-docs/ && \
    cd /home/akeneo/pim-docs/pim-community-dev-master/ && \
    php -d memory_limit=3G /home/akeneo/pim-docs/composer.phar install --no-suggest --ignore-platform-reqs && \
    cd /home/akeneo/pim-docs/pim-community-dev-master/ && php bin/console pim:installer:assets --env=prod && \
    mkdir /home/akeneo/pim-docs/pim-community-dev-master/public/css && \
    wget http://demo.akeneo.com/css/pim.css -P /home/akeneo/pim-docs/pim-community-dev-master/public/css && \
    #
    # Cleanup
    #
    rm -rf /root/.composer/cache && \
    cd /home/akeneo/pim-docs/pim-community-dev-master/ && ls | grep -v "vendor\|public" | xargs rm -rf
