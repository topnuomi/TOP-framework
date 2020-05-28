<?php

namespace top\library\database\driver;

use top\library\database\Base;
use top\library\database\ifs\DatabaseIfs;
use top\traits\Instance;

class Mysql extends Base implements DatabaseIfs
{

    use Instance;

    /**
     * 连接数据库
     * @param $config
     * @return $this
     */
    public function connect($config)
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
        $this->pdo = new \PDO($dsn, $config['user'], $config['passwd']);
        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        // 设置字符集
        $this->pdo->exec('SET NAMES ' . $config['charset']);

        return $this;
    }

    /**
     * 获取主键
     * @param $table
     * @param $database
     * @return string
     */
    public function getPk($table, $database)
    {
        $stmt = $this->pdo->query("SELECT COLUMN_NAME,COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE COLUMN_KEY = 'PRI' AND TABLE_NAME='{$table}' AND TABLE_SCHEMA='{$database}'");
        $stmt->execute();

        $column = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (!empty($column)) ? $column['COLUMN_NAME'] : '';
    }
}
