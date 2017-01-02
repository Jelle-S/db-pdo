<?php

namespace Jelle_S\DataBase\Statement;

/**
 * Description of Statement
 *
 * @author drupalpro
 */
class Statement extends \PDOStatement {

  /**
   * Fetches the given field of next row from a result set.
   * @param mixed $fieldname The fieldname or a zero-based index for the field number.
   * @return mixed
   * The field value on succes, FALSE on failure
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
   * @return array
   * An associative array with the row data.
   */
  public function fetchAssoc() {
    return $this->fetch(\PDO::FETCH_ASSOC);
  }

  public function fetchAllAssoc() {
    return $this->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function fetchTable($attributes = array()) {
    $table = "<table";
    $table .= !empty($table_id) ? " id='$table_id'" : '';
    $table .= !empty($table_class) ? " class='$table_class'" : '';
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