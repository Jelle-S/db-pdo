<?php

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 * Add a having clause to queries.
 *
 * @author Jelle Sebreghts
 */
trait Having {

  protected $having = '';

  /**
   * Add a having clause to the query.
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
   *   (optional) Leave empty if this is the first having clause of the query.
   *   Possible values "AND" and "OR". You can also use the andhaving and
   *   orhaving functions of this class.
   *
   * @return $this
   */
  public function having($field, $value = NULL, $operator = NULL, $concatenator = NULL) {
    if (empty($concatenator)) {
      $concatenator = "";
    }
    $this->having .= $this->condition($field, $value, $operator, $concatenator);
    return $this;
  }

  /**
   * Add an "and having" clause to the query.
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
  public function andhaving($field, $value = NULL, $operator = NULL) {
    return $this->having($field, $value, $operator, "AND");
  }

  /**
   * Add an "or having" clause to the query.
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
  public function orhaving($field, $value = NULL, $operator = NULL) {
    return $this->having($field, $value, $operator, "OR");
  }

}
