<?php

namespace Jelle_S\DataBase\Statement;

/**
 * Represents a prepared statement and, after the statement is executed, an
 * associated result set.
 *
 * @author Jelle Sebreghts
 */
class Statement extends \PDOStatement {

  /**
   * Fetches the given field of next row from a result set.
   *
   * @param int|string $fieldname
   *   The field name or a zero-based index for the field number.
   *
   * @return mixed
   *   The field value on success, FALSE on failure.
   */
  public function fetchField($fieldname = 0) {
    $data = $this->fetch(\PDO::FETCH_BOTH);
    if (!isset($data[$fieldname])) {
      $data[$fieldname] = FALSE;
    }
    return $data[$fieldname];
  }

  /**
   * Fetches the next row from a result set as an associative array.
   *
   * @return array
   *   An associative array with the row data.
   */
  public function fetchAssoc() {
    return $this->fetch(\PDO::FETCH_ASSOC);
  }

  /**
   * Fetches all rows of a result set as an associative array.
   *
   * @return array
   *   An array of associative arrays with the row data.
   */
  public function fetchAllAssoc() {
    return $this->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Fetch the result as an HTML table.
   *
   * @param array $attributes
   *   An associative array of HTML attributes.
   *
   * @return string
   *   The HTML table.
   */
  public function fetchTable($attributes = array()) {
    $table = "<table";
    foreach ($attributes as $attribute => $value) {
      if (is_array($value)) {
        //support multiple classes (e.g. class = "class1 class2").
        $value = implode(" ", $value);
      }
      $table .= " " . $attribute . "=\"" . $value . "\"";
    }
    $table .= ">\n";
    $tableheaders = "";
    $rows = "";
    $header = "";
    while ($row = $this->fetchAssoc()) {
      if (empty($tableheaders)) {
        $header .= "\t<tr>\n";
      }
      $rows .= "\t<tr>\n";
      foreach ($row as $fieldname => $field) {
        if (empty($tableheaders)) {
          $header .= "\t\t<th>" . ucfirst(strtolower($fieldname)) . "</th>\n";
        }
        $rows .= "\t\t<td>" . $field . "</td>\n";
      }
      $rows .= "\t</tr>\n";
      if (empty($tableheaders)) {
        $header .= "\t</tr>\n";
        $tableheaders .= $header;
      }
    }
    $table .= $tableheaders . $rows . "</table>\n";
    return $table;
  }

}