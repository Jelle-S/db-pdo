<?php

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 * Add where clauses to queries.
 *
 * @author Jelle Sebreghts
 */
trait Where {

  protected $where = '';

  /**
   * Add a where clause to the query.
   *
   * @param string $field
   *   The column to which this clause applies.
   * @param mixed $value
   *   (optional) The value to compare the column with. Leave this parameter and
   *   the operator parameter blank (NULL) to have the "IS NULL" operator (WHERE
   *   column IS NULL). If this parameter is an array and no operator is given
   *   the "IN" operator will be used (WHERE column IN (value1, value2). If this
   *   parameter is an array you can also specify the "NOT IN" operator as the
   *   next parameter (WHERE column NOT IN (value1, value2). If this parameter
   *   is a string and no operator is given the "=" operator will be used (WHERE
   *   column = value).
   * @param string $operator
   *   (optional) The operator that will be used (e.g. "IS NULL", "IN",
   *   "NOT IN", "=", "<>", ...).
   * @param string $concatenator
   *   (optional) Leave empty if this is the first where clause of the query.
   *   Possible values "AND" and "OR". You can also use the andwhere and orwhere
   *   functions of this class.
   *
   * @return $this
   */
  public function where($field, $value = NULL, $operator = NULL, $concatenator = NULL) {
    if (empty($concatenator)) {
      $concatenator = "";
    }
    $this->where .= $this->condition($field, $value, $operator, $concatenator);
    return $this;
  }

  /**
   * Add an "and where" clause to the query.
   *
   * @param string $field
   *   The column to which this clause applies.
   * @param mixed $value
   *   (optional) The value to compare the column with. Leave this parameter and
   *   the operator parameter blank (NULL) to have the "IS NULL" operator (WHERE
   *   column IS NULL). If this parameter is an array and no operator is given
   *   the "IN" operator will be used (WHERE column IN (value1, value2). If this
   *   parameter is an array you can also specify the "NOT IN" operator as the
   *   next parameter (WHERE column NOT IN (value1, value2). If this parameter
   *   is a string and no operator is given the "=" operator will be used (WHERE
   *   column = value).
   * @param string $operator
   *   (optional) The operator that will be used (e.g. "IS NULL", "IN",
   *   "NOT IN", "=", "<>", ...).
   *
   * @return $this
   */
  public function andwhere($field, $value = NULL, $operator = NULL) {
    return $this->where($field, $value, $operator, "AND");
  }

  /**
   * Add an "or where" clause to the query.
   *
   * @param string $field
   *   The column to which this clause applies.
   * @param mixed $value
   *   (optional) The value to compare the column with. Leave this parameter and
   *   the operator parameter blank (NULL) to have the "IS NULL" operator (WHERE
   *   column IS NULL). If this parameter is an array and no operator is given
   *   the "IN" operator will be used (WHERE column IN (value1, value2). If this
   *   parameter is an array you can also specify the "NOT IN" operator as the
   *   next parameter (WHERE column NOT IN (value1, value2). If this parameter
   *   is a string and no operator is given the "=" operator will be used (WHERE
   *   column = value).
   * @param string $operator
   *   (optional) The operator that will be used (e.g. "IS NULL", "IN",
   *   "NOT IN", "=", "<>", ...).
   *
   * @return $this
   */
  public function orwhere($field, $value = NULL, $operator = NULL) {
    return $this->where($field, $value, $operator, "OR");
  }

}
