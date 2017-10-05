#!/bin/bash

echo "Starting services"

docker-compose -f docker/compose.yml -p coding-challenge up
