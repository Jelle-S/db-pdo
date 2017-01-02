<?php

namespace Jelle_S\DataBase;

/**
 * Creates a database connection and executes queries.
 *
 * @author Jelle Sebreghts
 */
class Connection extends \PDO {

  protected $error;
  protected $errorCallbackFunction;
  protected $errorMsgFormat;

  /**
   * Creates a new \Jelle_S\DataBase\Connection object.
   *
   * @param string $dsn
   *   The dsn string.
   * @param string $user
   *   (optional) The database user name.
   * @param string $passwd
   *   (optional) The database password.
   * @param array $options
   *   (optional) A key => value array of driver-specific connection options.
   * @param string $errorCallbackFunction
   *   (optional) The callback function to show errors (e.g. "print", "echo", ...).
   * @param string $errorFormat
   *   (optional) The format to display errors ("html" or "text").
   */
  public function __construct($dsn, $user = NULL, $passwd = NULL, $errorCallbackFunction = NULL, $errorFormat = NULL) {
    $options = array(
      \PDO::ATTR_PERSISTENT => TRUE,
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    );

    if (empty($user)) {
      $user = "";
    }

    if (empty($passwd)) {
      $passwd = "";
    }

    if (empty($errorCallbackFunction)) {
      $errorCallbackFunction = "print_r";
    }

    if (empty($errorFormat)) {
      $errorFormat = "html";
    }

    if (strtolower($errorFormat) !== "html") {
      $errorFormat = "text";
    }

    $this->errorMsgFormat = strtolower($errorFormat);
    $this->errorCallbackFunction = $errorCallbackFunction;

    try {
      parent::__construct($dsn, $user, $passwd, $options);
      $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array('\Jelle_S\DataBase\Statement\Statement'));
    }
    catch (\PDOException $e) {
      $this->error = $e->getMessage();
    }
  }

  /**
   * Build a select query.
   *
   * @param string $table
   *   The table name.
   * @param array $fields
   *   (optional) An array with the column names you want to select as values.
   *
   * @return \Jelle_S\DataBase\Query\Select
   *   The select query.
   */
  public function select($table, array $fields = NULL) {
    $table = trim($table);
    $alias = '';
    if (strpos($table, " ")) {
      $table = trim(substr($table, 0, strpos($table, " ")));
      $alias = trim(substr($table, strpos($table, " ")));
    }
    $query = new Query\Select($this, $table, $alias);
    if (!empty($fields)) {
      $query->fields($fields);
    }
    return $query;
  }

  /**
   * Build an insert query.
   *
   * @param string $table
   *   The table to insert the data into.
   * @param array $fields
   *   (optional) An array with the column names as keys and the values you want
   *   to insert as values.
   *
   * @return \Jelle_S\DataBase\Query\Insert
   *   The insert query.
   */
  public function insert($table, array $fields = NULL) {
    $query = new Query\Insert($this, $table);
    if (!empty($fields)) {
      $query->fields($fields);
    }
    return $query;
  }

  /**
   * Build an update query.
   *
   * @param string $table
   *   The table you want to update.
   * @param array $fields
   *   (optional) An array with the column names as keys and the values you want
   *   to insert as values.
   * @return \Jelle_S\DataBase\Query\Update
   *   The update query.
   */
  public function update($table, array $fields = NULL) {
    $query = new Query\Update($this, $table);
    if (!empty($fields)) {
      $query->fields($fields);
    }
    return $query;
  }

  /**
   * Build a delete query.
   *
   * @param string $table
   *   The table you want to delete data from.
   *
   * @return \Jelle_S\DataBase\Query\Delete
   *   The delete query.
   */
  public function delete($table) {
    $query = new Query\Delete($this, $table);
    return $query;
  }

  /**
   * Execute the query.
   *
   * @param \Jelle_S\DataBase\Query\Query
   *   The query to execute.
   *
   * @return \Jelle_S\DataBase\Statement\Statement|FALSE
   *   FALSE on failure, returns a Statement on success.
   */
  public function run(Query\Query $query) {
    $this->error = "";

    try {
      $stmt = $this->prepare((string) $query);
      foreach ($query->getParameters() as $bind => $value) {
        switch (gettype($value)) {
          case 'integer':
            $type = \PDO::PARAM_INT;
            $value = (integer) $value;
            break;
          case 'string':
            $type = \PDO::PARAM_STR;
            $value = (string) $value;
            break;
          case 'boolean':
            $type = \PDO::PARAM_BOOL;
            $value = (boolean) $value;
            break;
          case 'NULL':
            $type = \PDO::PARAM_NULL;
            break;
        }
        $stmt->bindValue($bind, $value, $type);
      }
      if ($stmt->execute() !== FALSE) {
        return $stmt;
      }
    }
    catch (\PDOException $e) {
      print $e->getMessage();
      $this->error = $e->getMessage();
      $this->debug($query);
      return FALSE;
    }
  }

  /**
   * Prepares a statement for execution and returns a statement object.
   *
   * @param string $sql
   *   This must be a valid SQL statement for the target database server.
   *
   * @param array $driver_options [optional]
   *   This array holds one or more key=&gt;value pairs to set attribute values
   *   for the \PDOStatement object that this method returns. You would most
   *   commonly use this to set the \PDO::ATTR_CURSOR value to
   *   \PDO::CURSOR_SCROLL to request a scrollable cursor. Some drivers have
   *   driver specific options that may be set at prepare-time.
   *
   * @return \Jelle_S\DataBase\Statement\Statement|FALSE
   *   If the database server successfully prepares the statement unless
   *   otherwise specified in the $driver_options argument, this method returns
   *   a \Jelle_S\DataBase\Statement\Statement object. If the database server
   *   cannot successfully prepare the statement, this method returns FALSE or
   *   emits \PDOException (depending on error handling).
   */
  public function prepare($sql, $driver_options = array()) {
    if (!isset($driver_options[\PDO::ATTR_STATEMENT_CLASS])) {
      $driver_options[\PDO::ATTR_STATEMENT_CLASS] = array('\Jelle_S\DataBase\Statement\Statement');
    }
    return parent::prepare($sql, $driver_options);
  }

  /**
   * Display the encountered errors.
   *
   * @param \Jelle_S\DataBase\Query\Query
   *   The query to debug.
   */
  public function debug(Query\Query $query) {
    if (!empty($this->errorCallbackFunction)) {
      $error = array("Error" => $this->error);
      if (!empty($this->sql)) {
        $error["SQL Statement"] = (string) $query;
      }
      if (!empty($this->bind)) {
        $error["Bind Parameters"] = trim(print_r($query->getParameters(), TRUE));
      }

      $backtrace = debug_backtrace();
      if (!empty($backtrace)) {
        foreach ($backtrace as $info) {
          if ($info["file"] != __FILE__) {
            $error["Backtrace"] = $info["file"] . " at line " . $info["line"];
          }
        }
      }

      $msg = "";
      if ($this->errorMsgFormat == "html") {
        if (!empty($error["Bind Parameters"])) {
          $error["Bind Parameters"] = "<pre>" . $error["Bind Parameters"] . "</pre>";
        }
        $css = trim(file_get_contents(dirname(__FILE__) . "/error.css"));
        $msg .= '<style type="text/css">' . "\n" . $css . "\n</style>";
        $msg .= "\n" . '<div class="db-error">' . "\n\t<h3>SQL Error</h3>";
        foreach ($error as $key => $val) {
          $msg .= "\n\t<label>" . $key . ":</label>" . $val;
        }
        $msg .= "\n\t</div>\n</div>";
      }
      elseif ($this->errorMsgFormat == "text") {
        $msg .= "SQL Error\n" . str_repeat("-", 50);
        foreach ($error as $key => $val) {
          $msg .= "\n\n$key:\n$val";
        }
      }

      $func = $this->errorCallbackFunction;
      $func($msg);
    }
  }

  /**
   * Set the function and format to show errors.
   *
   * @param string $errorCallbackFunction
   *   The callback function (e.g. "print", "echo", ...).
   * @param string $errorMsgFormat
   * (optional) The format to display errors in ("html" or "text").
   *
   * @return $this
   */
  public function setErrorCallbackFunction($errorCallbackFunction, $errorMsgFormat = NULL) {
    if (empty($errorMsgFormat)) {
      $errorMsgFormat = "html";
    }
    //Variable functions for won't work with language constructs such as echo and print, so these are replaced with print_r.
    if (in_array(strtolower($errorCallbackFunction), array("echo", "print"))) {
      $errorCallbackFunction = "print_r";
    }

    if (function_exists($errorCallbackFunction)) {
      $this->errorCallbackFunction = $errorCallbackFunction;
      if (!in_array(strtolower($errorMsgFormat), array("html", "text"))) {
        $errorMsgFormat = "html";
      }
      $this->errorMsgFormat = $errorMsgFormat;
    }
    return $this;
  }

}
