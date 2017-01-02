<?php

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 *
 * @author drupalpro
 */
trait Where {
  protected $where = '';
  /**
   * Add a where clause to the query.
   * @param string $field The column to wich this clause applies.
   * @param mixed $value [optional]
   * <p>The value to compare the column with.</p>
   * <p>Leave this parameter and the operator parameter blank (NULL) to have the "IS NULL" operator (WHERE column IS NULL).</p>
   * <p>If this parameter is an array and no operator is given the "IN" operator will be used (WHERE column IN (value1, value2).</p>
   * <p>If this parameter is an array you can also specify the "NOT IN" operator as the next parameter (WHERE column NOT IN (value1, value2).</p>
   * <p>If this parameter is a string and no operator is given the "=" operator will be used (WHERE column = value).</p>
   * @param string $operator [optional] <p>The operator that will be used (e.g. "IS NULL", "IN", "NOT IN", "=", "<>", ...).</p>
   * @param string $concatenator [optional] <p>Leave empty if this is the first where clause of the query.</p>
   * <p>Possible values "AND" and "OR". You can also use the andwhere and orwhere functions of this class.</p>
   * @return db
   */
  public function where($field, $value = NULL, $operator = NULL, $concatenator = NULL) {
    if (empty($concatenator)) {
      $concatenator = "";
    }
    $this->where .= $this->condition($field, $value, $operator, $concatenator);
    return $this;
  }

  /**
   * Add a "and where" clause to the query.
   * @param string $field The column to wich this clause applies.
   * @param mixed $value [optional]
   * <p>The value to compare the column with.</p>
   * <p>Leave this parameter and the operator parameter blank (NULL) to have the "IS NULL" operator (WHERE column IS NULL).</p>
   * <p>If this parameter is an array and no operator is given the "IN" operator will be used (WHERE column IN (value1, value2).</p>
   * <p>If this parameter is an array you can also specify the "NOT IN" operator as the next parameter (WHERE column NOT IN (value1, value2).</p>
   * <p>If this parameter is a string and no operator is given the "=" operator will be used (WHERE column = value).</p>
   * @param string $operator [optional] <p>The operator that will be used (e.g. "IS NULL", "IN", "NOT IN", "=", "<>", ...).</p>
   * @return db
   */
  public function andwhere($field, $value = NULL, $operator = NULL) {
    return $this->where($field, $value, $operator, "AND");
  }

  /**
   * Add a "or where" clause to the query.
   * @param string $field The column to wich this clause applies.
   * @param mixed $value [optional]
   * <p>The value to compare the column with.</p>
   * <p>Leave this parameter and the operator parameter blank (NULL) to have the "IS NULL" operator (WHERE column IS NULL).</p>
   * <p>If this parameter is an array and no operator is given the "IN" operator will be used (WHERE column IN (value1, value2).</p>
   * <p>If this parameter is an array you can also specify the "NOT IN" operator as the next parameter (WHERE column NOT IN (value1, value2).</p>
   * <p>If this parameter is a string and no operator is given the "=" operator will be used (WHERE column = value).</p>
   * @param string $operator [optional] <p>The operator that will be used (e.g. "IS NULL", "IN", "NOT IN", "=", "<>", ...).</p>
   * @return db
   */
  public function orwhere($field, $value = NULL, $operator = NULL) {
    return $this->where($field, $value, $operator, "OR");
  }

}
