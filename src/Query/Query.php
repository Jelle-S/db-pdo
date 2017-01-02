<?php

namespace Jelle_S\DataBase\Query;

use Jelle_S\DataBase\Connection;

/**
 * Description of Query
 *
 * @author Jelle Sebreghts
 */
abstract class Query {

  protected $sql;
  protected $bind = [];
  protected $connection;

  public abstract function build();

  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  public function run() {
    return $this->connection->run($this);
  }

  /**
   * Helper function to assure the data to bind is an array.
   * @param mixed $bind
   * @return Array
   */
  protected function cleanup($bind) {
    if (!is_array($bind)) {
      if (!empty($bind)) {
        $bind = array($bind);
      }
      else {
        $bind = array();
      }
    }
    return $bind;
  }

  public function __toString() {
    if (!$this->sql) {
      $this->build();
    }
    return $this->sql;
  }

  public function getParameters() {
    return $this->bind;
  }

}
