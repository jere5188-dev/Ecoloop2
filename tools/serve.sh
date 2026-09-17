#!/usr/bin/env bash
# Start the EcoLoop dev server, detached, on port ${1:-8123}.
#   bash tools/serve.sh            # start
#   bash tools/serve.sh 8080       # start on another port
# Stop with: pkill -f "ecoloop-dev-server"
cd "$(dirname "$0")/.."
PORT="${1:-8123}"
pkill -f "ecoloop-dev-server" 2>/dev/null
setsid nohup php -d error_reporting=E_ALL -d display_errors=1 \
  -S "127.0.0.1:${PORT}" -t public \
  > /tmp/ecoloop-dev-server.log 2>&1 < /dev/null &
disown
sleep 2
printf 'EcoLoop dev server on http://127.0.0.1:%s (log: /tmp/ecoloop-dev-server.log)\n' "$PORT"
