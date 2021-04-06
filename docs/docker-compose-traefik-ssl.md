# Koillection - Traefik - SSL Docker-compose

This file is about having a docker-compose file compatible with an already-working [Traefik](https://doc.traefik.io/traefik/) setup and Letsencrypt integration through his integration with many cloud provider using dnsChallenge/httpChallenge or tlschallenge.


You need to know on which network your traefik is connected to connect Koillection to it "traefik_lan" in our example and also the name of your tls provider to have it adapted on the docker-compose file.

This example is using postgresql but you can use [mysql](https://hub.docker.com/_/mysql) also using his docker image. 
You need to set your own DB Password and store it in a secure location.

You can add another layer of security by adding [Authelia](https://www.authelia.com) to the docker-compose labels section which will redirect each new connection to Authelia first to be authenticated/filtered then you will be redirected to Koillection.

```yaml
traefik.http.routers.koillection.middlewares=authelia@docker
```

The documentation of both Authelia & Traefik are well documented enough to answer any questions.
The use of [Portainer](https://www.portainer.io) can also ease the usage of such a setup.
