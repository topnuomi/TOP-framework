<?php

namespace application\home\model;

use system\top\Model;

/**
 * 模型示例
 * Class Example
 * @package application\home\model
 */
class Example extends Model {

    protected $table = 'users';
    protected $pk = 'id';
    protected $map = [
        'type' => 'user_type'
    ];

    protected $insertHandle = [
        '' => 'time',
        '' => ['getIntTime', true]
    ];

    protected $updateHandle = [
        '' => ['getIntTime', true]
    ];

    // 出库
    protected $outHandle = [
        '' => [
            1 => '一',
        ]
    ];

    // 数据验证
    protected $validate = [
        '' => [
            ['notEqual', 0, 'tips'],
            ['notNull', 'tips']
        ],
        '' => ['notNull', 'tips']
    ];

    /**
     * 将字符串时间格式化为unix时间戳
     *
     * @param $param
     * @return false|int
     */
    public function getIntTime($param) {
        return strtotime($param);
    }

}