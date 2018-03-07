FROM debian:9-slim AS pim
MAINTAINER pierre.allard@akeneo.com
WORKDIR /home/akeneo/pim-docs/
ENV DEBIAN_FRONTEND=noninteractive

COPY build.sh /home/akeneo/pim-docs/build.sh

# Create environment for build with python
RUN chmod +x /home/akeneo/pim-docs/build.sh && \
    apt-get update && \
    apt-get install -y --no-install-recommends wget lsb-release apt-transport-https ca-certificates gnupg unzip ssh \
        rsync python python-setuptools && \
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
