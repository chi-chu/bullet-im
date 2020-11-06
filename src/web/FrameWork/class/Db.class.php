<?php

defined('APP_START') or exit('Access Denied');
define('PDO_DEBUG', true);

class Db
{
    protected $pdo;
    protected $cfg;
    protected $tablepre;
    protected $errors = array();

    public function __construct($filed = 'db') {
        global $_CONFIG;
        $this->cfg = $_CONFIG[$filed];
        $this->connect();
    }

    public function  __destruct() {

    }

    public function connect() {
        if (empty($this->cfg)) {
            exit("The database config is not found, Please checking 'data/config.php'");
        }
        $options = array();
        if ($this->cfg['pconnect']) {
            $options = array(PDO::ATTR_PERSISTENT => $this->cfg['pconnect']);
        }
        if (!class_exists('PDO')) {
            exit('The php PDO extension is not found, Please checking');
        }
        if (!in_array(strtolower($this->cfg['engine']), PDO::getAvailableDrivers())) {
            exit('no db driver supported');
        }
        if (!extension_loaded('pdo_'.strtolower($this->cfg['engine']))) {
            exit('The php pdo_'.$this->cfg['engine'].'extension isn`t loaded');
        }
        $dsn = strtolower($this->cfg['engine']).":dbname={$this->cfg['database']};host={$this->cfg['host']};port={$this->cfg['port']}";
        $this->pdo = new PDO($dsn, $this->cfg['username'], $this->cfg['password'], $options);
        $sql = 'SET NAMES '.$this->cfg['charset'].';';
        $this->pdo->exec($sql);
        $this->pdo->exec("SET sql_mode='';");
        if (PDO_DEBUG) {
            $info = array();
            $info['sql'] = $sql;
            $info['error'] = $this->pdo->errorInfo();
            $this->debug(false, $info);
        }
    }

    public function prepare($sql) {
        $statement = $this->pdo->prepare($sql);
        return $statement;
    }


    public function query($sql, $params = array()) {
        if (empty($params)) {
            $result = $this->pdo->exec($sql);
            if (PDO_DEBUG) {
                $info = array();
                $info['sql'] = $sql;
                $info['error'] = $this->pdo->errorInfo();
                $this->debug(false, $info);
            }
            return $result;
        }
        $statement = $this->prepare($sql);
        $result = $statement->execute($params);
        if (PDO_DEBUG) {
            $info = array();
            $info['sql'] = $sql;
            $info['params'] = $params;
            $info['error'] = $statement->errorInfo();
            $this->debug(false, $info);
        }
        if (!$result) {
            return false;
        } else {
            return $statement->rowCount();
        }
    }


    public function fetchcolumn($sql, $params = array(), $column = 0) {
        $statement = $this->prepare($sql);
        $result = $statement->execute($params);
        if (PDO_DEBUG) {
            $info = array();
            $info['sql'] = $sql;
            $info['params'] = $params;
            $info['error'] = $statement->errorInfo();
            $this->debug(false, $info);
        }
        if (!$result) {
            return false;
        } else {
            return $statement->fetchColumn($column);
        }
    }


    private function fetch($sql, $params = array()) {
        $starttime = microtime();
        $statement = $this->prepare($sql);
        $result = $statement->execute($params);
        if (PDO_DEBUG) {
            $info = array();
            $info['sql'] = $sql;
            $info['params'] = $params;
            $info['error'] = $statement->errorInfo();
            $this->debug(false, $info);
        }
        if (!$result) {
            return false;
        } else {
            return $statement->fetch(pdo::FETCH_ASSOC);
        }
    }


    private function fetchall($sql, $params = array(), $keyfield = '') {
        $statement = $this->prepare($sql);
        $result = $statement->execute($params);
        if (PDO_DEBUG) {
            $info = array();
            $info['sql'] = $sql;
            $info['params'] = $params;
            $info['error'] = $statement->errorInfo();
            $this->debug(false, $info);
        }
        if (!$result) {
            return false;
        } else {
            if (empty($keyfield)) {
                return $statement->fetchAll(pdo::FETCH_ASSOC);
            } else {
                $temp = $statement->fetchAll(pdo::FETCH_ASSOC);
                $rs = array();
                if (!empty($temp)) {
                    foreach ($temp as $key => &$row) {
                        if (isset($row[$keyfield])) {
                            $rs[$row[$keyfield]] = $row;
                        } else {
                            $rs[] = $row;
                        }
                    }
                }
                return $rs;
            }
        }
    }

    public function select($tablename, $params = array(), $limit= array(), $fields = array()) {
        $select = '*';
        if (!empty($fields)) {
            if (is_array($fields)) {
                $select = '`' . implode('`,`', $fields) . '`';
            } else {
                $select = $fields;
            }
        }
        $condition = $this->implode($params, 'AND');
        $limitsql = ' LIMIT 1';
        if (!empty($limit)) {
            $limitsql = ' LIMIT '.$limit[0].','.$limit[1];
        }
        $sql = "SELECT {$select} FROM " . $this->tablename($tablename) . (!empty($condition['fields']) ? " WHERE {$condition['fields']}" : '') . $limitsql;
        return $this->fetchall($sql, $condition['params']);
    }

    public function count($tablename, $params = array()) {
        $condition = $this->implode($params, 'AND');
        $sql = 'SELECT count(*) FROM ' . $this->tablename($tablename) . (!empty($condition['fields']) ? " WHERE {$condition['fields']}" : '');
        return $this->fetch($sql, $condition['params'])['count(*)'];
    }

    public function update($table, $data = array(), $params = array(), $glue = 'AND') {
        $fields = $this->implode($data, ',');
        $condition = $this->implode($params, $glue);
        $params = array_merge($fields['params'], $condition['params']);
        $sql = 'UPDATE ' . $this->tablename($table) . ' SET '.$fields['fields'];
        $sql .= $condition['fields'] ? ' WHERE ' . $condition['fields'] : '';
        return $this->query($sql, $params);
    }


    public function insert($table, $data = array(), $replace = FALSE) {
        $cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
        $condition = $this->implode($data, ',');
        return $this->query($cmd . $this->tablename($table) . " SET {$condition['fields']}", $condition['params']);
    }

    public function insertid() {
        return $this->pdo->lastInsertId();
    }


    public function delete($table, $params = array(), $glue = 'AND') {
        $condition = $this->implode($params, $glue);
        $sql = 'DELETE FROM ' . $this->tablename($table);
        $sql .= $condition['fields'] ? ' WHERE ' . $condition['fields'] : '';
        return $this->query($sql, $condition['params']);
    }


    public function begin() {
        $this->pdo->beginTransaction();
    }


    public function commit() {
        $this->pdo->commit();
    }


    public function rollback() {
        $this->pdo->rollBack();
    }


    private function implode($params, $glue = ',') {
        $result = array('fields' => ' 1 ', 'params' => array());
        $split = '';
        $suffix = '';
        if (in_array(strtolower($glue), array('and', 'or'))) {
            $suffix = '__';
        }
        if (!is_array($params)) {
            $result['fields'] = $params;
            return $result;
        }
        if (is_array($params)) {
            $result['fields'] = '';
            foreach ($params as $fields => $value) {
                if (is_array($value)) {
                    $result['fields'] .= $split . "`$fields` IN ('" . implode("','", $value) . "')";
                } else {
                    $result['fields'] .= $split . "`$fields` =  :{$suffix}$fields";
                    $split = ' ' . $glue . ' ';
                    $result['params'][":{$suffix}$fields"] = is_null($value) ? '' : $value;
                }
            }
        }
        return $result;
    }


    public function run($sql, $stuff = 'ims_') {
        if (!isset($sql) || empty($sql)) return;

        $sql = str_replace("\r", "\n", str_replace(' ' . $stuff, ' ' . $this->tablepre, $sql));
        $sql = str_replace("\r", "\n", str_replace(' `' . $stuff, ' `' . $this->tablepre, $sql));
        $ret = array();
        $num = 0;
        $sql = preg_replace("/\;[ \f\t\v]+/", ';', $sql);
        foreach (explode(";\n", trim($sql)) as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            foreach ($queries as $query) {
                $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0] . $query[1] == '--') ? '' : $query;
            }
            $num++;
        }
        unset($sql);
        foreach ($ret as $query) {
            $query = trim($query);
            if ($query) {
                $this->query($query, array());
            }
        }
    }


    public function fieldexists($tablename, $fieldname) {
        $isexists = $this->fetch("DESCRIBE " . $this->tablename($tablename) . " `{$fieldname}`", array());
        return !empty($isexists) ? true : false;
    }


    public function indexexists($tablename, $indexname) {
        if (!empty($indexname)) {
            $indexs = $this->fetchall("SHOW INDEX FROM " . $this->tablename($tablename), array(), '');
            if (!empty($indexs) && is_array($indexs)) {
                foreach ($indexs as $row) {
                    if ($row['Key_name'] == $indexname) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function tableexists($table) {
        if (!empty($table)) {
            $data = $this->fetch("SHOW TABLES LIKE '{$this->tablepre}{$table}'", array());
            if (!empty($data)) {
                $data = array_values($data);
                $tablename = $this->tablepre . $table;
                if (in_array($tablename, $data)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function tablename($table) {
        return "`{$this->tablepre}{$table}`";
    }


    public function debug($output = true, $append = array()) {
        if (!empty($append)) {
            $output = false;
            array_push($this->errors, $append);
        }
        if ($output) {
            print_r($this->errors);
        } else {
            if (!empty($append['error'][1])) {
                $traces = debug_backtrace();
                $ts = '';
                foreach ($traces as $trace) {
                    $trace['file'] = str_replace('\\', '/', $trace['file']);
                    $trace['file'] = str_replace(IA_ROOT, '', $trace['file']);
                    $ts .= "file: {$trace['file']}; line: {$trace['line']}; <br />";
                }
                $params = var_export($append['params'], true);
                message("SQL: <br/>{$append['sql']}<hr/>Params: <br/>{$params}<hr/>SQL Error: <br/>{$append['error'][2]}<hr/>Traces: <br/>{$ts}");
            }
        }
        return $this->errors;
    }

    private function performance($sql, $runtime = 0) {
        if ($runtime == 0) {
            return false;
        }
        if (strpos($sql, 'core_performance')) {
            return false;
        }
        return true;
    }
}
