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

# Copy script
COPY build.sh /home/akeneo/pim-docs/build.sh
RUN chmod +x /home/akeneo/pim-docs/build.sh
