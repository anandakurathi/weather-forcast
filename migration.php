<?php
require 'bootstrap.php';

$statement = <<<EOS
    CREATE TABLE routee_tokens (
      id int(11) unsigned NOT NULL AUTO_INCREMENT,
      token varchar(200) NOT NULL,
      expiry datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      created_at timestamp DEFAULT CURRENT_TIMESTAMP,
      updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (id)
    ) ENGINE=InnoDB;

    CREATE TABLE jobs (
      id int(11) unsigned NOT NULL AUTO_INCREMENT,
      payload text,
      status enum('NEW','PROCESSING','DONE','ERROR') DEFAULT NULL,
      whento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (id)
    ) ENGINE=InnoDB;

EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}
