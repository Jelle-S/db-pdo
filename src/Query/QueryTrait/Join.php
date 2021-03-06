<?php

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 * Add join clauses to queries.
 *
 * @author Jelle Sebreghts
 */
trait Join {

  protected $jointables = [];

  /**
   * Add a join clause to the query.
   *
   * @param string $table
   *   The table to join with.
   * @param string $condition
   *   (optional) The join condition.
   *
   * @return $this
   */
  public function join($table, $condition = NULL) {
    return $this->addJoin('INNER', $table, $condition);
  }

  /**
   * Add an inner join clause to the query.
   *
   * @param string $table
   *   The table to join with.
   * @param string $condition
   *   (optional) The join condition.
   *
   * @return $this
   */
  public function innerJoin($table, $condition = NULL) {
    return $this->addJoin('INNER', $table, $condition);
  }

  /**
   * Add a left join clause to the query.
   *
   * @param string $table
   *   The table to join with.
   * @param string $condition
   * (optional) The join condition.
   *
   * @return $this
   */
  public function leftJoin($table, $condition = NULL) {
    return $this->addJoin('LEFT OUTER', $table, $condition);
  }

  /**
   * Add a right join clause to the query.
   *
   * @param string $table
   *   The table to join with.
   * @param string $condition
   *   (optional) The join condition.
   *
   * @return $this
   */
  public function rightJoin($table, $condition = NULL) {
    return $this->addJoin('RIGHT OUTER', $table, $condition);
  }

  /**
   * Add a join clause to the query.
   *
   * @param string $type
   *   The join type ("INNER", "LEFT OUTER", "RIGHT OUTER").
   * @param string $table
   *   The table to join with.
   * @param string $condition
   *   (optional) The join condition.
   *
   * @return $this
   */
  public function addJoin($type, $table, $condition = NULL) {
    $orig_alias = trim(substr($table, strpos($table, " ")));
    $alias = $orig_alias;
    $alias_candidate = $alias;
    $count = 2;
    while (!empty($this->jointables[$alias_candidate])) {
      $alias_candidate = $alias . '_' . $count++;
    }

    $alias = $alias_candidate;
    $condition = str_replace($orig_alias, $alias, $condition);

    $this->jointables[$alias] = array(
      'join type' => $type,
      'table' => trim(substr($table, 0, strpos($table, " "))),
      'alias' => $alias,
      'condition' => $condition,
    );

    return $this;
  }

}
