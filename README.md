# ConnXus Coding Challenge

**Author:** [Amrit Panesar](mailto:me@amrit.be)

**License:** [MIT](./LICENSE)


## Prerequisites

This app requires a working Docker environment, which includes:

1. docker, latest
2. docker-compose, latest
3. docker-machine, latest (if on Windows/macOS)

These prerequisites can all be satisfied with Docker Toolbox.


## Execution

Simply run `start.sh` in your docker command-line environment.

A webserver will spawn at `:8080` of your local machine or at the IP address docker-machine provisioned


## Postman

A Postman Collection and Environment file are found in the [postman](./postman) directory.

The environment file has a key named `BaseURL` which points to the standard Docker-machine IP: `192.168.99.100`
 