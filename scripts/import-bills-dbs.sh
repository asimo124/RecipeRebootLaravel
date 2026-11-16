#!/usr/bin/env bash
# Load BillsSite MySQL dumps into the Docker `db` container.
#
# Put the dumps in docker/mysql/dumps/ named after the database:
#   docker/mysql/dumps/asimo124_bills.sql       (or .sql.gz)
#   docker/mysql/dumps/asimo124_bills_test.sql  (or .sql.gz)
#
# Usage (from the repo root, with `docker compose up -d` running):
#   scripts/import-bills-dbs.sh                  # imports every DB that has a dump
#   scripts/import-bills-dbs.sh asimo124_bills   # just one
#
# Each database is DROPPED and recreated before import. Dumps taken from
# MariaDB on the Debian server are patched on the fly for MySQL 8
# (MariaDB-only collations, DEFINER clauses, sandbox-mode header).
set -euo pipefail

cd "$(dirname "$0")/.."
DUMP_DIR=docker/mysql/dumps

if [ "$#" -gt 0 ]; then
  DBS=("$@")
else
  DBS=(asimo124_bills asimo124_bills_test)
fi

mysql_root() {
  docker compose exec -T db sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$@"' mysql "$@"
}

imported=0
for db in "${DBS[@]}"; do
  if [ -f "$DUMP_DIR/$db.sql.gz" ]; then
    reader=(gunzip -c "$DUMP_DIR/$db.sql.gz")
  elif [ -f "$DUMP_DIR/$db.sql" ]; then
    reader=(cat "$DUMP_DIR/$db.sql")
  else
    echo "skip  $db (no $DUMP_DIR/$db.sql[.gz])"
    continue
  fi

  echo "reset $db"
  mysql_root -e "DROP DATABASE IF EXISTS \`$db\`;
    CREATE DATABASE \`$db\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

  echo "load  $db"
  "${reader[@]}" \
    | sed -E \
        -e '/^\/\*M?!999999\\- enable the sandbox mode \*\//d' \
        -e 's/utf8mb4_uca1400_[a-z_]+/utf8mb4_unicode_ci/g' \
        -e 's/utf8mb3_uca1400_[a-z_]+/utf8mb3_unicode_ci/g' \
        -e 's/DEFINER=`[^`]*`@`[^`]*`//g' \
        -e '/^(CREATE DATABASE|USE) /d' \
    | mysql_root "$db"

  tables=$(mysql_root -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$db';")
  echo "done  $db ($tables tables)"
  imported=$((imported + 1))
done

[ "$imported" -gt 0 ] || { echo "No dumps imported." >&2; exit 1; }
