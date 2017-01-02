<?php

namespace Jelle_S\DataBase\Query;

use Jelle_S\DataBase\Connection;

/**
 * Represents an SQL query.
 *
 * @author Jelle Sebreghts
 */
abstract class Query {

  protected $sql;
  protected $bind = [];
  protected $connection;

  /**
   * Creates a new \Jelle_S\DataBase\Query\Query object.
   *
   * @param \Jelle_S\DataBase\Connection $connection
   *   The connection on which to execute this query.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Build the query.
   *
   * @return $this
   */
  public abstract function build();

  /**
   * Execute the Query.
   *
   * @return \Jelle_S\DataBase\Statement\Statement|FALSE
   *   FALSE on failure, returns a Statement on success.
   */
  public function run() {
    return $this->connection->run($this);
  }

  /**
   * Helper function to assure the data to bind is an array.
   *
   * @param mixed $bind
   *   The data to bind.
   *
   * @return array
   *   The sanitized data to bind.
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

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    if (!$this->sql) {
      $this->build();
    }
    return $this->sql;
  }

  /**
   * Get the parameters to bind.
   *
   * @return array
   *   An associative array of the parameters to bind.
   */
  public function getParameters() {
    return $this->bind;
  }

}
