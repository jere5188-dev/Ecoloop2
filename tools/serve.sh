#!/usr/bin/env bash
# ============================================================================
# EcoLoop dev server helper.
#
#   bash tools/serve.sh              start on port 8000
#   bash tools/serve.sh 8080         start on port 8080
#   bash tools/serve.sh stop         stop a running server
#
# This is only a convenience wrapper. The canonical way to run EcoLoop is:
#   php -S localhost:8000 -t public
# ============================================================================
set -uo pipefail

cd "$(dirname "$0")/.."

PIDFILE="${TMPDIR:-/tmp}/ecoloop-dev-server.pid"
LOGFILE="${TMPDIR:-/tmp}/ecoloop-dev-server.log"

stop() {
  if [ -f "$PIDFILE" ]; then
    PID=$(cat "$PIDFILE")
    # Only signal it if that PID is actually still our server.
    if kill -0 "$PID" 2>/dev/null; then
      kill "$PID" 2>/dev/null && echo "Stopped EcoLoop dev server (pid $PID)."
    else
      echo "No running server for pid $PID."
    fi
    rm -f "$PIDFILE"
  else
    echo "No pid file at $PIDFILE — nothing to stop."
  fi
}

if [ "${1:-}" = "stop" ]; then
  stop
  exit 0
fi

PORT="${1:-8000}"

# Replace an existing instance rather than fighting it for the port.
[ -f "$PIDFILE" ] && stop >/dev/null 2>&1

setsid nohup php -S "127.0.0.1:${PORT}" -t public \
  > "$LOGFILE" 2>&1 < /dev/null &
PID=$!
echo "$PID" > "$PIDFILE"
disown 2>/dev/null || true

sleep 2

if ! kill -0 "$PID" 2>/dev/null; then
  echo "Server failed to start. Last lines of $LOGFILE:" >&2
  tail -5 "$LOGFILE" >&2
  rm -f "$PIDFILE"
  exit 1
fi

cat <<EOF
EcoLoop dev server running.

  URL   http://127.0.0.1:${PORT}
  pid   ${PID}
  log   ${LOGFILE}

Stop it with:  bash tools/serve.sh stop
EOF
