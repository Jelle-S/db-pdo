<?php

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 * Add a limit clause to queries.
 *
 * @author Jelle Sebreghts
 */
trait Limit {

  protected $limit = '';

  /**
   * Add a limit clause to the query.
   *
   * @param int $limit
   *   The limit.
   * @param int $range
   *   The range.
   *
   * @return $this
   */
  public function limit($limit, $range = NULL) {
    if (empty($range) || $this->type == 'update' || $this->type == 'delete') {
      if (is_numeric($limit)) {
        $this->limit = $limit;
      }
    }
    else {
      if (is_numeric($limit) && is_numeric($range)) {
        $this->limit = $limit . ', ' . $range;
      }
    }
    return $this;
  }

}
