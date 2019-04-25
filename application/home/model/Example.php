<?php

namespace application\home\model;

use system\library\Load;
use system\top\Model;

/**
 * 模型示例
 * Class Example
 * @package application\home\model
 */
class Example extends Model {

    protected $table = 'permission';
    protected $pk = 'id';
    protected $map = [
        'type' => 'contract_type'
    ];

    protected $insertHandle = [
        'create_time' => 'time',
        'process_month' => ['getProcessMonth', true],
        'start_time' => 'time',
        'end_time' => ['getIntTime', true],
    ];

    protected $updateHandle = [
        'start_time' => ['getIntTime', true],
        'end_time' => ['getIntTime', true]
    ];

    // 出库
    protected $outHandle = [
        'intention_level' => [
            0 => '-',
            1 => 'A',
            2 => 'B',
            3 => 'C',
            4 => 'D'
        ]
    ];

    // 数据验证
    protected $validate = [
        'contract_type' => [
            ['notEqual', 0, '请选择合同类型'],
            ['notNull', '请选择合同类型']
        ],
        'contract_project' => ['notNull', '合同项目类型不能为空'],
        'contract_number' => ['notNull', '合同编号不能为空'],
        'contract_money' => [
            ['notEqual', 0, '请填写合同金额'],
            ['notNull', '请填写合同金额']
        ],
        'end_time' => [
            ['notEqual', 0, '请选择到期时间'],
            ['notNull', '请选择到期时间']
        ]
    ];

    /**
     * 获取年月字符串
     * @return false|string
     */
    protected function getProcessMonth() {
        return date('Y-m', time());
    }

    /**
     * 将字符串时间格式化为unix时间戳
     *
     * @param $param
     * @return false|int
     */
    public function getIntTime($param) {
        return strtotime($param);
    }

    /**
     * 获取列表
     * @return array
     */
    public function getLists() {
        return $this->select();
    }

    /**
     * 更新签单信息
     *
     * @param $id
     * @return bool
     */
    public function updatePermission($id) {
        return $this->update(function ($data) {
                if (isset($data['start_time']))
                    $data['start_time'] = strtotime($data['start_time']);
                if (isset($data['end_time']))
                    $data['end_time'] = strtotime($data['end_time']);
                return $data;
            }, $id) !== false;
    }

    /**
     * 获取签单表中当前用户的不重复月份（月份由高到低排序）
     *
     * @param string $type
     * @param string $keywords
     * @param bool $limit
     * @return mixed
     */
    public function getPermissionMonth($type = '', $keywords = '', $limit = false) {
        $where = [
            'this.status' => 1,
            'c.status' => 1,
            'c.customer_type' => Customer::PERMISSION
        ];
        if (check_type(Users::SALE) || $type == 0)
            $where = ['c.uid' => get_uid()];
        if ($type)
            $where['this.contract_type'] = $type;
        if ($keywords)
            $where['c.person_tel'] = ['like', $keywords];
        $data = $this->select(function ($query) use ($where, $limit) {
            $query->distinct('this.process_month');
            $query->join('left', 'customer', 'c')->on('this.cid = c.id');
            $query->where($where)->order('this.id desc')->limit($limit);
        });
        return array_reduce($data, function ($result, $value) {
            return array_merge($result, array_values($value));
        }, []);
    }

    /**
     * 删除签单操作
     * @param $id
     * @return bool
     * @throws \system\library\exception\BaseException
     */
    public function deletePermission($id) {
        $cid = $this->field('cid')->find($id)['cid'];
        $res = $this->update(['status' => 0], $id);
        if ($res !== false) {
            // 如果被删除的是该客户最后一个签单，则将客户改变为旧客户（进入客户总表）
            $count = $this->where(['cid' => $cid, 'status' => 1])->count();
            if ($count == 0) {
                $customer = Load::model('Customer');
                $data = ['uid' => 0, 'status' => 0, 'process_month' => date('Y-m', time())];
                if ($customer->update($data, $cid))
                    return true;
                $this->message = '客户资料更新失败';
                return false;
            }
            return true;
        }
        $this->message = '删除失败';
        return false;
    }


}