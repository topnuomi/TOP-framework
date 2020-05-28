<?php

namespace top\library\database\ifs;

/**
 * 数据库操作接口
 * @author topnuomi 2018年11月19日
 */
interface DatabaseIfs
{

    /**
     * 连接数据库
     * @param $config
     * @return mixed
     */
    public function connect($config);

    /**
     * 获取主键
     * @param $table
     * @param $database
     * @return mixed
     */
    public function getPk($table, $database);

}
