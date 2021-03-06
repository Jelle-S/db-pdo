<?php

namespace Jelle_S\DataBase\Query\QueryTrait;

/**
 * Render a condition clause.
 *
 * @author Jelle Sebreghts
 */
trait Condition {

  /**
   * Helper function to render a condition clause.
   *
   * @param string $field
   *   The column to which this clause applies.
   * @param mixed $value
   *   (optional) The value to compare the column with. Leave this parameter and
   *   the operator parameter blank (NULL) to have the "IS NULL" operator. If
   *   this parameter is an array and no operator is given the "IN" operator
   *   will be used. If this parameter is an array you can also specify the
   *   "NOT IN" operator as the next parameter. If this parameter is a string
   *   and no operator is given the "=" operator will be used.
   * @param string $operator
   *   (optional) The operator that will be used (e.g. "IS NULL", "IN",
   *   "NOT IN", "=", "<>", ...).
   *
   * @return string
   *   A condition build based on the given parameters.
   */
  protected function condition($field, $value, $operator, $concatenator) {
    if (!isset($operator) || $operator == "IN" || $operator == "NOT IN") {
      if (is_array($value)) {
        if (!isset($operator)) {
          $operator = 'IN';
        }
        $v = '(';
        $i = 0;
        foreach ($value as $val) {
          $i++;
          $v .= ':' . $field . $i . ', ';
          $bind[':' . $field . $i] = $val;
        }
        $v = substr($v, 0, -2);
        $v .= ')';
        $placeholder = $v;
      }
      elseif (!isset($value)) {
        $operator = 'IS NULL';
      }
      else {
        $operator = '=';
      }
    }
    if (!isset($placeholder)) {
      $placeholder = ':' . $field;
      $placeholder = str_replace('.', '', $placeholder);
      $i = 0;
      while (isset($this->bind[$placeholder])) {
        $i++;
        $placeholder = ':' . $field . $i;
        $placeholder = str_replace('.', '', $placeholder);
      }
      $bind[$placeholder] = $value;
    }

    $this->bind += $bind;
    if (!empty($concatenator)) {
      $concatenator = " " . trim($concatenator) . " ";
    }
    return $concatenator . $field . " " . $operator . " " . $placeholder;
  }

}
