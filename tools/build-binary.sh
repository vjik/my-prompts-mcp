#!/bin/sh
set -eu

IMAGE=${DOCKER_IMAGE:-ghcr.io/vjik/my-prompts-mcp-dev:latest}
ROOT_DIR=$(cd "$(dirname "$0")/.." && pwd)
DOCKER_RUN_PARAMS="--rm -v $ROOT_DIR:/app --user $(id -u):$(id -g)"

docker run $DOCKER_RUN_PARAMS $IMAGE box compile

mkdir -p "$ROOT_DIR/runtime/static-php-cli"
docker run $DOCKER_RUN_PARAMS \
  --workdir=/app/runtime/static-php-cli \
  $IMAGE \
  spc micro:combine \
    --with-micro=/builder/buildroot/bin/micro.sfx \
    --output=/app/build/my-prompts-mcp \
    /app/build/my-prompts-mcp.phar
