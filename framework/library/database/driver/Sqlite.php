<?php

namespace top\library\database\driver;

use top\library\database\Base;
use top\library\database\ifs\DatabaseIfs;
use top\traits\Instance;

class Sqlite extends Base implements DatabaseIfs
{

    use Instance;

    /**
     * 连接数据库
     * @param $config
     * @return $this
     */
    public function connect($config)
    {
        $dsn = "sqlite:{$config['dbname']}";
        $this->pdo = new \PDO($dsn);
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
        $stmt = $this->pdo->query("PRAGMA TABLE_INFO('$table')");
        $stmt->execute();

        $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $pk = '';
        foreach ($columns as $column) {
            if ($column['pk'] == 1) {
                $pk = $column['name'];
                break;
            }
        }
        return $pk;
    }
}
