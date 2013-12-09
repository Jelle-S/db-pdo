<?php
class db extends PDO {
    private $error;
    private $sql;
    private $bind = array();
    private $type;
    private $table;
    private $alias;
    private $jointables;
    private $fields = array();
    private $where = "";
    private $groupby;
    private $having = "";
    private $orderby;
    private $limit;
    private $on_duplicate;
    private $stmt;
    private $errorCallbackFunction;
    private $errorMsgFormat;

    /**
     * Create a new db object.
     * @param string $dsn The dsn string.
     * @param string $user [optional] <p>The database user name.</p>
     * @param string $passwd [optional] <p>The database password.</p>
     * @param array $options [optional] <p>A key=>value array of driver-specific connection options.<p>
     * @param string $errorCallbackFunction [optional] <p>The callback function to show errors (e.g. "print", "echo", ...).</p>
     * @param string $errorFormat [optional] <p>The format to display errors ("html" or "text").</p>
     */
    public function __construct($dsn, $user=NULL, $passwd=NULL, $errorCallbackFunction = NULL, $errorFormat = NULL) {
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        );

        if(empty($user)){
            $user = "";
        }

        if(empty($passwd)){
            $passwd = "";
        }

        if(empty ($errorCallbackFunction)){
            $errorCallbackFunction = "print_r";
        }

        if(empty ($errorFormat)){
            $errorFormat = "html";
        }

        if (strtolower($errorFormat) !== "html"){
            $errorFormat == "text";
        }

        $this->errorMsgFormat == strtolower($errorFormat);
        $this->errorCallbackFunction = $errorCallbackFunction;

        try {
            parent::__construct($dsn, $user, $passwd, $options);
            $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('dbStatement'));
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }
    /**
     * Prepares a statement for execution and returns a statement object
     * @param string $statement <p>
     * This must be a valid SQL statement for the target database server.
     * </p>
     * @param array $driver_options [optional] <p>
     * This array holds one or more key=&gt;value pairs to set
     * attribute values for the PDOStatement object that this method
     * returns. You would most commonly use this to set the
     * PDO::ATTR_CURSOR value to
     * PDO::CURSOR_SCROLL to request a scrollable cursor.
     * Some drivers have driver specific options that may be set at
     * prepare-time.
     * </p>
     * @return dbStatement If the database server successfully prepares the statement unless otherwise specified in the $driver_options argument,
     * db::prepare returns a
     * dbStatement object.
     * If the database server cannot successfully prepare the statement,
     * db::prepare returns false or emits
     * PDOException (depending on error handling).
     * </p>
     * <p>
     * Emulated prepared statements does not communicate with the database server
     * so db::prepare does not check the statement.
     */
    public function prepare($sql, $driver_options = array()) {
        if(!isset($driver_options[PDO::ATTR_STATEMENT_CLASS])){
            $driver_options[PDO::ATTR_STATEMENT_CLASS] = array('dbStatement');
        }
        return parent::prepare($sql, $driver_options);
      }

    /**
     * Build a select query.
     * @param string $table The table name.
     * @param array $fields [optional] <p>An array with the column names you want to select as values.</p>
     * @return db
     */
    public function select($table, array $fields = NULL) {
        $this->reset();
        $this->type = 'select';
        $table = trim($table);
        if(strpos($table, " ")){
            $this->table = trim(substr($table, 0, strpos($table, " ")));
            $this->alias = trim(substr($table, strpos($table, " ")));
        }
        else{
            $this->table = $table;
        }
        if (!empty($fields)){
            $this->fields($fields);
        }
        return $this;
    }

    /**
     * Build an insert query.
     * @param string $table The table to insert the data into.
     * @param array $fields [optional] <p>An array with the column names as keys and the values you want to insert as values.</p>
     * @return db
     */
    public function insert($table, array $fields = NULL) {
        $this->reset();
        $this->type = 'insert';
        $this->table = $table;
        if(!empty($fields)){
            $this->fields($fields);
        }
       return $this;
    }

    /**
     * Build an update query.
     * @param string $table The table you want to update.
     * @param array $fields [optional] <p>An array with the column names as keys and the values you want to insert as values.</p>
     * @return db
     */
    public function update($table, array $fields = NULL) {
        $this->reset();
        $this->type = 'update';
        $this->table = $table;
        if(!empty($fields)){
            $this->fields($fields);
        }
        return $this;
    }

    /**
     * Build a delete query.
     * @param string $table The table you want to delete data from.
     * @return db
     */
    public function delete($table) {
        $this->reset();
        $this->type = 'delete';
        $this->table = $table;
        return $this;
    }

    /**
     * Add fields to the query
     * @param array $fields <p>An array with the column names as keys and the values you want to insert as values for update and insert statements.</p><p>An array with the column names you want to select as values for select statements.</p>
     * @return db
     */
    public function fields(array $fields){
        switch ($this->type) {
            case 'insert':
            case 'update':
                if(!empty($fields)){
                    $this->fields += array_keys($fields);
                    //$this->sql .=  " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ")";
                    $bind = array();
                    foreach($this->fields as $field) {
                        $key = str_replace('.', '', $field);
                        $i = 0;
                        while(isset($this->bind[":$key"]) || isset($bind[":$key"])){
                            $key = str_replace('.', '', $field) . $i;
                        }
                        $bind[":$key"] = $fields[$field];
                    }
                    $this->bind += $this->cleanup($bind);
                }
                break;

            case 'select':
                if(!empty($fields)){
                    $this->fields += $fields;
                }
                break;

            default:
                break;
        }
        return $this;
    }

    /**
     * Add a join part to the query.
     * @param string $table The table to join with.
     * @param string $condition [optional] <p>The join condition.</p>
     * @return db
     */
    public function join($table, $condition = NULL) {
        return $this->addJoin('INNER', $table, $condition);
    }

    /**
     * Add an inner join part to the query.
     * @param string $table The table to join with.
     * @param string $condition [optional] <p>The join condition.</p>
     * @return db
     */
    public function innerJoin($table, $condition = NULL) {
        return $this->addJoin('INNER', $table, $condition);
    }

    /**
     * Add a left join part to the query.
     * @param string $table The table to join with.
     * @param string $condition [optional] <p>The join condition.</p>
     * @return db
     */
    public function leftJoin($table, $condition = NULL) {
        return $this->addJoin('LEFT OUTER', $table, $condition);
    }

    /**
     * Add a right join part to the query.
     * @param string $table The table to join with.
     * @param string $condition [optional] <p>The join condition.</p>
     * @return db
     */
    public function rightJoin($table, $condition = NULL) {
        return $this->addJoin('RIGHT OUTER', $table, $condition);
    }

    /**
     * Add a join part to the query.
     * @param string $table The table to join with.
     * @param string $type The join type ("INNER", "LEFT OUTER", "RIGHT OUTER").
     * @param string $condition [optional] <p>The join condition.</p>
     * @return db
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
    public function where($field, $value = NULL, $operator = NULL, $concatenator = NULL){
        if(empty($concatenator)){
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
    public function andwhere($field, $value = NULL, $operator = NULL){
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
    public function orwhere($field, $value = NULL, $operator = NULL){
        return $this->where($field, $value, $operator, "OR");
    }


    /**
     * Add a group by clause to the query.
     * @param mixed $fields A string containing the comma-separated columns (or a single column name) or an array containing the columns (or a single column).
     * @return db
     */
    public function groupby($fields){
        if(!is_array($fields)){
            $fields = array($fields);
        }
        foreach($fields as $f){
            if(!empty($this->groupby)){
                $this->groupby .= ", " . $f;
            }
            else {
                $this->groupby = $f;
            }
        }
        return $this;
    }

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
    public function having($field, $value = NULL, $operator = NULL, $concatenator = NULL){
        if(empty($concatenator)){
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
    public function andhaving($field, $value = NULL, $operator = NULL){
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
    public function orhaving($field, $value = NULL, $operator = NULL){
        return $this->having($field, $value, $operator, "OR");
    }

    /**
     * Add an order by clause to the query.
     * @param mixed $fields A string containing the comma-separated columns (or a single column name) or an array containing the columns (or a single column).
     * @param string $order [optional] <p>"ASC" or "DESC"</p>.
     * @return db
     */
    public function orderby($fields, $order = NULL){
        if(empty($order)){
            $order = "ASC";
        }
        if(!is_array($fields)){
            $fields = array($fields);
        }
        foreach($fields as $f){
            $f = $f . " " . $order;
            if(!empty($this->orderby)){
                $this->orderby .= ", " . $f;
            }
            else {
                $this->orderby = $f;
            }
        }
        return $this;
    }

    /**
     * Add a limit clause to the query.
     * @param Integer $limit
     * @param Integer $range
     * @return db
     */
    public function limit($limit, $range=NULL){
        if(empty($range) || $this->type == 'update' || $this->type == 'delete'){
            if(is_numeric($limit)){
                $this->limit = $limit;
            }
        }
        else{
            if(is_numeric($limit) && is_numeric($range)){
                $this->limit = $limit . ', ' . $range;
            }

        }
        return $this;
    }

    /**
     * If you specify ON DUPLICATE KEY UPDATE, and a row is inserted that would cause a duplicate value in a UNIQUE index or PRIMARY KEY, an UPDATE of the old row is performed.
     * @param array $fields An array with the column names as key and the the values they should update to as values.
     * @return db 
     */
    public function on_duplicate_key_update(array $fields){
        $f = array_keys($fields);
        $bind = array();
        foreach($f as $field) {
            $key = str_replace('.', '', $field);
            $i = 0;
            while(isset($this->bind[":$key"])){
                $i++;
                $key = str_replace('.', '', $field) . $i;
            }
            $bind[":$key"] = $fields[$field];
            $this->on_duplicate .= $field . ' = :' . $key . ', ';
        }
        $this->on_duplicate = substr($this->on_duplicate, 0, -2);
        $this->bind += $this->cleanup($bind);
        return $this;
    }

    /**
     * Execute the query.
     * @return mixed
     * Returns false on failure, returns a dbStatement on succes.
     */
    public function run() {
        if(empty($this->sql)){
            $this->build();
        }
        $this->sql = trim($this->sql);
        $this->bind = $this->cleanup($this->bind);
        $this->error = "";

        try {
            $stmt =$this->prepare($this->sql);
            foreach($this->bind as $bind => $value){
                switch (gettype($value)){
                    case 'integer':
                        $type = PDO::PARAM_INT;
                        $value = (integer)$value;
                        break;
                    case 'string':
                        $type = PDO::PARAM_STR;
                        $value = (string)$value;
                        break;
                    case 'boolean':
                        $type = PDO::PARAM_BOOL;
                        $value = (boolean)$value;
                        break;
                    case 'NULL':
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        NULL;
                        break;
                }
                $stmt->bindValue($bind, $value, $type);
            }
            if($stmt->execute() !== false) {
                return $stmt;
            }
        } catch (PDOException $e) {
            print $e->getMessage();
            $this->error = $e->getMessage();
            $this->debug();
            return false;
        }
    }

    /**
     * Build the query. If this is not executed before the run() method, it will be called by that method.
     * @return db
     */
    public function build(){
        switch ($this->type) {

            case 'select':
                if(is_array($this->fields)){
                    if(empty($this->fields)){
                        $this->fields = array("*");
                    }
                    $this->fields = implode($this->fields, ", ");
                }
                $this->sql = "SELECT " . $this->fields . " FROM " . $this->table;
                if(!empty($this->alias)){
                    $this->sql .= " " . $this->alias;
                }
                if(!empty($this->jointables)){
                    foreach($this->jointables as $table){
                        $this->sql .= " " . $table['join type'] . " JOIN " . $table['table'] . " " . $table['alias'] . " ON (" . $table['condition'] . ")";
                    }
                }
                if (!empty($this->where)){
                    $this->sql .= " WHERE " . $this->where;
                }
                if(!empty($this->groupby)){
                    $this->sql .=  " GROUP BY " . $this->groupby;
                }
                if(!empty($this->having)){
                    $this->sql .= " HAVING " . $this->having;
                }
                if(!empty($this->orderby)){
                     $this->sql .= " ORDER BY " . $this->orderby;
                }
                if(!empty($this->limit)){
                     $this->sql .= " LIMIT " . $this->limit;
                }
                $this->sql .= ';';
                break;

            case 'update':
                $this->sql = "UPDATE " . $this->table . " SET";
                foreach($this->fields as $f){
                    $this->sql .= " " . $f . '= :' . $f . ",";
                }
                $this->sql = substr($this->sql, 0, -1);
                if (!empty($this->where)){
                    $this->sql .= " WHERE " . $this->where;
                }
                if(!empty($this->orderby)){
                     $this->sql .= " ORDER BY " . $this->orderby;
                }
                if(!empty($this->limit)){
                     $this->sql .= " LIMIT " . $this->limit;
                }
                $this->sql .= ';';
                break;

            case 'insert':
                $this->sql = "INSERT INTO " . $this->table . " (" . implode($this->fields, ", ") . ") VALUES (:" . implode($this->fields, ", :") . ")";
                if(!empty($this->on_duplicate)){
                    $this->sql .= " ON DUPLICATE KEY UPDATE " . $this->on_duplicate;
                }
                $this->sql .= ';';
                break;

            case 'delete':
                $this->sql = "DELETE FROM " . $this->table;
                if (!empty($this->where)){
                    $this->sql .= " WHERE " . $this->where;
                }
                if(!empty($this->orderby)){
                     $this->sql .= " ORDER BY " . $this->orderby;
                }
                if(!empty($this->limit)){
                     $this->sql .= " LIMIT " . $this->limit;
                }
                $this->sql .= ';';
                break;

            default:
                break;

        }
        return $this;
    }

    /**
     * Set the callback function and format to show errors (e.g. "print", "echo", ...).
     * @param string $errorCallbackFunction The callback function.
     * @param string $errorMsgFormat The format to display errors in ("html" or "text").
     * @return db
     */
    public function setErrorCallbackFunction($errorCallbackFunction, $errorMsgFormat=NULL) {
        if(empty($errorMsgFormat)){
            $errorMsgFormat = "html";
        }
        //Variable functions for won't work with language constructs such as echo and print, so these are replaced with print_r.
        if(in_array(strtolower($errorCallbackFunction), array("echo", "print"))){
            $errorCallbackFunction = "print_r";
        }

        if(function_exists($errorCallbackFunction)) {
            $this->errorCallbackFunction = $errorCallbackFunction;
            if(!in_array(strtolower($errorMsgFormat), array("html", "text"))){
                $errorMsgFormat = "html";
            }
            $this->errorMsgFormat = $errorMsgFormat;
        }
        return $this;
    }

    /**
     * Helper function for all the where and having functions of this class.
     * @param string $field The column to wich this clause applies.
     * @param mixed $value [optional]
     * <p>The value to compare the column with.</p>
     * <p>Leave this parameter and the operator parameter blank (NULL) to have the "IS NULL" operator.</p>
     * <p>If this parameter is an array and no operator is given the "IN" operator will be used.</p>
     * <p>If this parameter is an array you can also specify the "NOT IN" operator as the next parameter.</p>
     * <p>If this parameter is a string and no operator is given the "=" operator will be used.</p>
     * @param string $operator [optional] <p>The operator that will be used (e.g. "IS NULL", "IN", "NOT IN", "=", "<>", ...).</p>
     * @return string
     * A condition build based on the given parameters.
     */
    private function condition($field, $value, $operator, $concatenator){
        if (!isset($operator) || $operator == "IN" || $operator == "NOT IN") {
            if (is_array($value)) {
                if(!isset($operator)){
                    $operator = 'IN';
                }
                $v = '(';
                $i=0;
                foreach($value as $val){
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
        if(!isset($placeholder)){
            $placeholder = ':' . $field;
            $placeholder = str_replace('.', '', $placeholder);
            $i = 0;
            while(isset($this->bind[$placeholder])){
                $i++;
                $placeholder = ':' . $field . $i;
                $placeholder = str_replace('.', '', $placeholder);
            }
            $bind[$placeholder] = $value;
        }
        
        $this->bind += $bind;
        if(!empty($concatenator)){
            $concatenator = " " . trim($concatenator) . " ";
        }
        return $concatenator . $field . " " . $operator. " " . $placeholder;
    }
    
    /**
     * Display the encountered errors.
     */
    private function debug() {
        if(!empty($this->errorCallbackFunction)) {
            $error = array("Error" => $this->error);
            if(!empty($this->sql))
                $error["SQL Statement"] = $this->sql;
            if(!empty($this->bind))
                $error["Bind Parameters"] = trim(print_r($this->bind, true));

            $backtrace = debug_backtrace();
            if(!empty($backtrace)) {
                foreach($backtrace as $info) {
                    if($info["file"] != __FILE__)
                        $error["Backtrace"] = $info["file"] . " at line " . $info["line"];
                }
            }

            $msg = "";
            if($this->errorMsgFormat == "html") {
                if(!empty($error["Bind Parameters"]))
                    $error["Bind Parameters"] = "<pre>" . $error["Bind Parameters"] . "</pre>";
                $css = trim(file_get_contents(dirname(__FILE__) . "/error.css"));
                $msg .= '<style type="text/css">' . "\n" . $css . "\n</style>";
                $msg .= "\n" . '<div class="db-error">' . "\n\t<h3>SQL Error</h3>";
                foreach($error as $key => $val)
                    $msg .= "\n\t<label>" . $key . ":</label>" . $val;
                $msg .= "\n\t</div>\n</div>";
            }
            elseif($this->errorMsgFormat == "text") {
                $msg .= "SQL Error\n" . str_repeat("-", 50);
                foreach($error as $key => $val)
                    $msg .= "\n\n$key:\n$val";
            }

            $func = $this->errorCallbackFunction;
            $func($msg);
        }
    }

    /**
     * Helper function to assure the data to bind is an array.
     * @param mixed $bind
     * @return Array
     */
    private function cleanup($bind) {
        if(!is_array($bind)) {
            if(!empty($bind))
                $bind = array($bind);
            else
                $bind = array();
        }
        return $bind;
    }

    /**
     * Helper function to reset all fields before we start building a new query.
     */
    private function reset(){
        $this->error = NULL;
        $this->sql = NULL;
        $this->bind = array();
        $this->type = NULL;
        $this->table = NULL;
        $this->alias = NULL;
        $this->jointables = NULL;
        $this->fields = array();
        $this->where = "";
        $this->groupby = NULL;
        $this->having = "";
        $this->orderby = NULL;
        $this->limit = NULL;
        $this->on_duplicate = NULL;
        $this->stmt = NULL;
    }

    public function __toString() {
        return '<pre>' . print_r($this, TRUE) . '</pre>';
    }

}

class dbStatement extends PDOStatement {

    /**
     * Fetches the given field of next row from a result set.
     * @param mixed $fieldname The fieldname or a zero-based index for the field number.
     * @return mixed
     * The field value on succes, FALSE on failure
     */
    public function fetchField($fieldname = 0) {
        $data = $this->fetch(PDO::FETCH_BOTH);
        if(!isset($data[$fieldname])){
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
        return $this->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAllAssoc(){
        return $this->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchTable($attributes = array()){
        $table = "<table";
        $table .= !empty($table_id) ? " id='$table_id'" : '';
        $table .= !empty($table_class) ? " class='$table_class'" : '';
        foreach($attributes as $attribute => $value){
            if(is_array($value)){
                //support multiple classes (e.g. class = "class1 class2").
                $value = implode(" ", $value);
            }
            $table .= " " . $attribute . "=\"" . $value . "\"";
        }
        $table .= ">\n";
        $tableheaders = "";
        $rows = "";
        $header = "";
        while($row = $this->fetchAssoc()){
            if(empty($tableheaders)){
                $header .= "\t<tr>\n";
            }
            $rows .= "\t<tr>\n";
            foreach ($row as $fieldname => $field){
                if(empty($tableheaders)){
                    $header .= "\t\t<th>" . ucfirst(strtolower($fieldname)) . "</th>\n";
                }
                $rows .= "\t\t<td>" . $field . "</td>\n";
            }
            $rows .= "\t</tr>\n";
            if(empty ($tableheaders)){
                $header .= "\t</tr>\n";
                $tableheaders .= $header;
            }
        }
        $table .= $tableheaders . $rows . "</table>\n";
        return $table;
    }

}
?>
