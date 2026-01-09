#!/bin/bash
export DEV_MODE=1
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --force-recreate
