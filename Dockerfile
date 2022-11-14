FROM akeneo/pim-php-dev:master
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
        python \
        ssh  \
        rsync  \
        curl \
        python3-pip && \
    apt-get clean && apt-get --yes --quiet autoremove --purge && \
    rm -rf /var/lib/apt/lists/* /var/log/* /tmp/* /usr/share/locale/*

RUN pip3 install --upgrade setuptools wheel
RUN pip3 install --upgrade sphinx

