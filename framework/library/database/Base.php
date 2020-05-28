<?php

namespace top\library\database;

use top\library\exception\DatabaseException;

abstract class Base
{

    protected $insertSql = 'INSERT INTO [table] ([field]) VALUES ([data])';
    protected $updateSql = 'UPDATE [table] [join] SET [data] [where] [order] [limit]';
    protected $selectSql = 'SELECT [field] FROM [table] [join] [where] [order] [limit]';
    protected $deleteSql = 'DELETE FROM [table] [join] [where] [order] [limit]';

    /**
     * PDO连接
     * @var \PDO
     */
    protected $pdo = null;

    private $sql = null;

    /**
     * 连接数据库
     * @return $this
     */
    public function connect($config)
    {
        return $this;
    }

    /**
     * 获取主键
     * @return string
     */
    public function getPk($table, $database)
    {
        return '';
    }

    /**
     * 插入记录
     * @param $table
     * @param $data
     * @return mixed
     */
    public function insert($table, $data)
    {
        $this->executeInsert($table, $data);
        return $this->pdo->lastInsertId();
    }

    /**
     * 更新记录
     * @param $table
     * @param $alias
     * @param $join
     * @param $where
     * @param $order
     * @param $limit
     * @param $data
     * @return int
     * @throws DatabaseException
     */
    public function update($table, $alias, $join, $where, $order, $limit, $data)
    {
        $stmt = $this->executeUpdate($table, $alias, $join, $where, $order, $limit, $data);
        return $stmt->rowCount();
    }

    /**
     * 查找一条记录
     * @param $table
     * @param $alias
     * @param $distinct
     * @param $field
     * @param $join
     * @param $where
     * @param $order
     * @return mixed
     * @throws DatabaseException
     */
    public function find($table, $alias, $distinct, $field, $join, $where, $order)
    {
        $stmt = $this->executeSelect($table, $alias, $distinct, $field, $join, $where, $order, '1');
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * 查找全部
     * @param $table
     * @param $alias
     * @param $distinct
     * @param $field
     * @param $join
     * @param $where
     * @param $order
     * @param $limit
     * @return array|mixed
     * @throws DatabaseException
     */
    public function select($table, $alias, $distinct, $field, $join, $where, $order, $limit)
    {
        $stmt = $this->executeSelect($table, $alias, $distinct, $field, $join, $where, $order, $limit);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 删除记录
     * @param $table
     * @param $alias
     * @param $join
     * @param $where
     * @param $order
     * @param $limit
     * @return int|mixed
     * @throws DatabaseException
     */
    public function delete($table, $alias, $join, $where, $order, $limit)
    {
        $stmt = $this->executeDelete($table, $alias, $join, $where, $order, $limit);
        return $stmt->rowCount();
    }

    /**
     * 执行一条SQL
     * @param $query
     * @param $params
     * @return bool|\PDOStatement
     * @throws DatabaseException
     */
    public function query($query, $params)
    {
        if (false === ($stmt = $this->pdo->prepare($query))) {
            throw new DatabaseException($this->pdo->errorInfo()[2]);
        }
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * 返回PDO
     * @return \PDO
     */
    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * 事务
     */
    public function begin()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * 提交
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * 回滚
     */
    public function rollback()
    {
        return $this->pdo->rollBack();
    }

    /**
     * 公共方法
     * @param $table
     * @param $alias
     * @param $field
     * @param $join
     * @param $where
     * @param $type
     * @return mixed
     * @throws DatabaseException
     */
    public function common($table, $alias, $field, $join, $where, $type)
    {
        $where = $this->parseWhere($where);
        $field = $type . '(`' . $this->parseField(false, $field) . '`)';
        $sql = str_replace(
            ['[field]', '[table]', '[join]', '[where]', '[order]', '[limit]'],
            [
                $field,
                $this->parseTable($table, $alias),
                $this->parseJoin($join),
                $where['where'],
                '',
                '',
            ], $this->selectSql
        );

        if (false === ($stmt = $this->pdo->prepare($sql))) {
            throw new DatabaseException($this->pdo->errorInfo()[2]);
        }
        $stmt->execute($where['data']);
        return $stmt->fetch(\PDO::FETCH_ASSOC)[$field];
    }

    /**
     * 预处理并执行插入操作
     * @param $table
     * @param $data
     * @return bool|\PDOStatement
     * @throws DatabaseException
     */
    private function executeInsert($table, $data)
    {
        $insertData = $this->parseInsertData($data);
        $sql = str_replace(
            ['[table]', '[field]', '[data]'],
            [
                $this->parseTable($table, ''),
                $insertData['field'],
                $insertData['values'],
            ], $this->insertSql
        );

        if (false === ($stmt = $this->pdo->prepare($sql))) {
            throw new DatabaseException($this->pdo->errorInfo()[2]);
        }
        $stmt->execute($insertData['data']);
        return $stmt;
    }

    /**
     * 预处理并执行更新操作
     * @param $table
     * @param $alias
     * @param $join
     * @param $where
     * @param $order
     * @param $limit
     * @param $data
     * @return bool|\PDOStatement
     * @throws DatabaseException
     */
    private function executeUpdate($table, $alias, $join, $where, $order, $limit, $data)
    {
        $where = $this->parseWhere($where);
        $updateData = $this->parseUpdateData($alias, $data);
        $sql = str_replace(
            ['[table]', '[data]', '[join]', '[where]', '[order]', '[limit]'],
            [
                $this->parseTable($table, $alias),
                $updateData['field'],
                $this->parseJoin($join),
                $where['where'],
                $this->parseOrder($order),
                $this->parseLimit($limit),
            ], $this->updateSql
        );

        if (false === ($stmt = $this->pdo->prepare($sql))) {
            throw new DatabaseException($this->pdo->errorInfo()[2]);
        }
        $bindData = array_merge($updateData['data'], $where['data']);
        $stmt->execute($bindData);
        return $stmt;
    }

    /**
     * 预处理并执行查询操作
     * @param $table
     * @param $alias
     * @param $distinct
     * @param $field
     * @param $join
     * @param $where
     * @param $order
     * @param $limit
     * @return bool|\PDOStatement
     * @throws DatabaseException
     */
    private function executeSelect($table, $alias, $distinct, $field, $join, $where, $order, $limit)
    {
        $where = $this->parseWhere($where);

        $sql = str_replace(
            ['[table]', '[field]', '[join]', '[where]', '[order]', '[limit]'],
            [
                $this->parseTable($table, $alias),
                $this->parseField($distinct, $field),
                $this->parseJoin($join),
                $where['where'],
                $this->parseOrder($order),
                $this->parseLimit($limit),
            ], $this->selectSql
        );

        if (false === ($stmt = $this->pdo->prepare($sql))) {
            throw new DatabaseException($this->pdo->errorInfo()[2]);
        }
        $stmt->execute($where['data']);
        return $stmt;
    }

    /**
     * 预处理并执行删除操作
     * @param $table
     * @param $alias
     * @param $join
     * @param $where
     * @param $order
     * @param $limit
     * @return bool|\PDOStatement
     * @throws DatabaseException
     */
    private function executeDelete($table, $alias, $join, $where, $order, $limit)
    {
        $where = $this->parseWhere($where);

        $sql = str_replace(
            ['[table]', '[join]', '[where]', '[order]', '[limit]'],
            [
                $this->parseTable($table, $alias),
                $this->parseJoin($join),
                $where['where'],
                $this->parseOrder($order),
                $this->parseLimit($limit),
            ], $this->deleteSql);

        if (false === ($stmt = $this->pdo->prepare($sql))) {
            throw new DatabaseException($this->pdo->errorInfo()[2]);
        }
        $stmt->execute($where['data']);
        return $stmt;
    }

    /**
     * 解析table
     * @param $table
     * @param $alias
     * @return string
     */
    protected function parseTable($table, $alias)
    {
        return "`$table`" . ($alias ? ' ' . $alias : '');
    }

    /**
     * 解析field
     * @param $distinct
     * @param $field
     * @return string
     */
    protected function parseField($distinct, $field)
    {
        if (is_array($field)) {
            $field = implode(',', $field);
        } else if (!$field) {
            $field = '*';
        }
        return ($distinct ? 'DISTINCT ' : '') . $field;
    }

    /**
     * 解析join
     * @param $allJoin
     * @return string
     */
    protected function parseJoin($allJoin)
    {
        $joinString = '';
        foreach ($allJoin as $key => $join) {
            $joinString .= $join[2] . ' JOIN ' . $join[0] . ' ON ' . $join[1] . ' ';
        }
        return mb_substr($joinString, 0, mb_strlen($joinString, 'utf-8') - 1, 'utf-8');
    }

    /**
     * 解析where
     * @param $allWhere
     * @return array
     */
    protected function parseWhere($allWhere)
    {
        $whereArray = [
            'temp' => [],
            'where' => '',
            'data' => [],
        ];
        foreach ($allWhere as $key => $item) {
            foreach ($item as $field => $value) {
                if (is_array($value)) { // 数组
                    $whereArray['temp'][] = $field . ' ' . $value[0] . ' ?';
                    $whereArray['data'][] = $value[1];
                } else {
                    $whereArray['temp'][] = $field . ' = ?';
                    $whereArray['data'][] = $value;
                }
            }
        }
        $whereArray['where'] = (!empty($whereArray['temp'])) ? 'WHERE ' . implode(' AND ', $whereArray['temp']) : '';
        unset($whereArray['temp']);
        return $whereArray;
    }

    /**
     * 解析order
     * @param $order
     * @return string
     */
    protected function parseOrder($order)
    {
        return ($order) ? ' ORDER BY ' . $order : '';
    }

    /**
     * 解析limit
     * @param $limit
     * @return string
     */
    protected function parseLimit($limit)
    {
        if (is_array($limit)) {
            $limit = implode(',', $limit);
        }
        return ($limit) ? 'LIMIT ' . $limit : '';
    }

    /**
     * 解析insert参数
     * @param $data
     * @return array
     */
    protected function parseInsertData($data)
    {
        $insertData = [
            'field' => [],
            'values' => [],
            'data' => [],
        ];
        foreach ($data as $field => $value) {
            $insertData['field'][] = '`' . $field . '`';
            $insertData['values'][] = '?';
            $insertData['data'][] = $value;
        }
        $insertData['field'] = implode(',', $insertData['field']);
        $insertData['values'] = implode(',', $insertData['values']);
        return $insertData;
    }

    /**
     * 解析update参数
     * @param $alias
     * @param $data
     * @return array
     */
    protected function parseUpdateData($alias, $data)
    {
        $updateData = [
            'field' => [],
            'data' => [],
        ];
        foreach ($data as $field => $value) {
            if (strstr($field, '.')) {
                $field = explode('.', $field);
                $updateData['field'][] = $field[0] . '.`' . $field[1] . '` = ?';
            } else {
                $updateData['field'][] = ($alias ? $alias . '.' : '') . '`' . $field . '` = ?';
            }
            $updateData['data'][] = $value;
        }
        $updateData['field'] = implode(',', $updateData['field']);
        return $updateData;
    }

    /**
     * 返回最后执行的sql
     * @return string
     */
    public function sql()
    {
        return $this->sql;
    }

    /**
     * 关闭数据库连接
     * @return mixed
     */
    public function close()
    {
        // TODO: Implement close() method.
    }
}
