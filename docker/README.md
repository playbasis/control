# Playbasis's Control docker-compose project

## Nginx Dockerfile

This repository contains **Dockerfile** of Playbasis's [control](http://www.pbapp.net/)


### Base Docker Image

* [ubuntu](https://hub.docker.com/_/ubuntu/)
* [php](https://hub.docker.com/_/php/)
* [nginx](https://hub.docker.com/_/nginx/)


### Installation

1. Install [Docker](https://www.docker.com/).
2. Clone/Fork this repository.

### Usage (via docker-compose)

check certain parameter in `docker-compose.yml` and set it to match your config.

    docker-compose build
    docker-compose up -d

After few seconds, open `http://<host>` to see the login page.
