<?php

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 *
 * @author Jelle Sebreghts
 */
trait Having {
  protected $having = '';
  /**
   * Add a having clause to the query.
   * @param string $field The column to wich this clause applies.
   * @param mixed $value [optional]
   * <p>The value to compare the column with.</p>
   * <p>Leave this parameter and the operator parameter blank (NULL) to have the "IS NULL" operator (HAVING column IS NULL).</p>
   * <p>If this parameter is an array and no operator is given the "IN" operator will be used (HAVING column IN (value1, value2).</p>
   * <p>If this parameter is an array you can also specify the "NOT IN" operator as the next parameter (HAVING column IN (value1, value2).</p>
   * <p>If this parameter is a string and no operator is given the "=" operator will be used (HAVING column = value).</p>
   * @param string $operator [optional] <p>The operator that will be used (e.g. "IS NULL", "IN", "NOT IN", "=", "<>", ...).</p>
   * @param string $concatenator [optional] <p>Leave empty if this is the first having clause of the query.</p>
   * <p>Possible values "AND" and "OR". You can also use the andhaving and orhaving functions of this class.</p>
   * @return db
   */
  public function having($field, $value = NULL, $operator = NULL, $concatenator = NULL) {
    if (empty($concatenator)) {
      $concatenator = "";
    }
    $this->having .= $this->condition($field, $value, $operator, $concatenator);
    return $this;
  }

  /**
   * Add a  "and having" clause to the query.
   * @param string $field The column to wich this clause applies.
   * @param mixed $value [optional]
   * <p>The value to compare the column with.</p>
   * <p>Leave this parameter and the operator parameter blank (NULL) to have the "IS NULL" operator (HAVING column IS NULL).</p>
   * <p>If this parameter is an array and no operator is given the "IN" operator will be used (HAVING column IN (value1, value2).</p>
   * <p>If this parameter is an array you can also specify the "NOT IN" operator as the next parameter (HAVING column IN (value1, value2).</p>
   * <p>If this parameter is a string and no operator is given the "=" operator will be used (HAVING column = value).</p>
   * @param string $operator [optional] <p>The operator that will be used (e.g. "IS NULL", "IN", "NOT IN", "=", "<>", ...).</p>
   * @return db
   */
  public function andhaving($field, $value = NULL, $operator = NULL) {
    return $this->having($field, $value, $operator, "AND");
  }

  /**
   * Add a  "or having" clause to the query.
   * @param string $field The column to wich this clause applies.
   * @param mixed $value [optional]
   * <p>The value to compare the column with.</p>
   * <p>Leave this parameter and the operator parameter blank (NULL) to have the "IS NULL" operator (HAVING column IS NULL).</p>
   * <p>If this parameter is an array and no operator is given the "IN" operator will be used (HAVING column IN (value1, value2).</p>
   * <p>If this parameter is an array you can also specify the "NOT IN" operator as the next parameter (HAVING column IN (value1, value2).</p>
   * <p>If this parameter is a string and no operator is given the "=" operator will be used (HAVING column = value).</p>
   * @param string $operator [optional] <p>The operator that will be used (e.g. "IS NULL", "IN", "NOT IN", "=", "<>", ...).</p>
   * @return db
   */
  public function orhaving($field, $value = NULL, $operator = NULL) {
    return $this->having($field, $value, $operator, "OR");
  }

}
