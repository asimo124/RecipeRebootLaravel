-- Applied on first init of an empty MySQL data volume.
-- Creates the BillsSite databases empty; load data with scripts/import-bills-dbs.sh.
CREATE DATABASE IF NOT EXISTS asimo124_bills CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS asimo124_bills_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
