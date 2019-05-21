<?php
namespace system\library\database\ifs;

/**
 * 数据库操作接口
 *
 * @author topnuomi 2018年11月19日
 */
interface DatabaseIfs {

    /**
     * 连接数据库
     *
     * @param array $config            
     */
    public function connect($config);

    /**
     * 插入记录
     *
     * @param string $table            
     * @param array $data            
     */
    public function insert($table, $data);

    /**
     * 更新记录
     *
     * @param string $table            
     * @param array $join            
     * @param string|array $on            
     * @param string|array $where            
     * @param string $order            
     * @param string $limit            
     * @param array $data            
     */
    public function update($table, $join, $on, $where, $order, $limit, $data);

    /**
     * 查找一条记录
     *
     * @param string $table            
     * @param string|array $field            
     * @param array $join            
     * @param string|array $on            
     * @param string|array $where            
     * @param string $order            
     */
    public function find($table, $distinct, $field, $join, $on, $where, $order);

    /**
     * 查找全部
     *
     * @param string $table            
     * @param string|array $field            
     * @param array $join            
     * @param string|array $on            
     * @param string|array $where            
     * @param string $order            
     * @param string $limit            
     */
    public function select($table, $distinct, $field, $join, $on, $where, $order, $limit);

    /**
     * 删除记录
     * 
     * @param string|array $effect            
     * @param string $table            
     * @param array $join            
     * @param string|array $on            
     * @param string|array $where            
     * @param string $order            
     * @param string $limit            
     */
    public function delete($effect, $table, $join, $on, $where, $order, $limit);

    /**
     * 执行一条SQL
     *
     * @param string $query            
     */
    public function query($query);

    /**
     * 关闭数据库连接
     */
    public function close();
}