<?php

namespace top\extend;


/**
 * 分页类
 * @author topnuomi 2018年11月28日
 */
class Page
{

    /**
     * 记录总数
     * @var int
     */
    private $total = 0;

    /**
     * 总页数
     * @var int
     */
    private $totalPage = 0;

    /**
     * 当前页码
     * @var int
     */
    public $page = 1;

    /**
     * 开始记录
     * @var int
     */
    public $firstRow = 0;

    /**
     * 每页显示记录数
     * @var int
     */
    public $listRow = 0;

    public function __construct($listRow, $total)
    {
        $this->listRow = $listRow;
        $this->total = $total;
        $this->page = (isset($_GET['p']) && $_GET['p']) ? (int)$_GET['p'] : ((isset($_POST['p']) && $_POST['p']) ? (int)$_POST['p'] : 1);
        $this->totalPage = $this->totalPage();
        $this->firstRow = $this->firstRow();
    }

    /**
     * 计算开始记录数
     * @return float|int
     */
    private function firstRow()
    {
        return ($this->page - 1) * $this->listRow;
    }

    /**
     * 计算总页数
     * @return float
     */
    private function totalPage()
    {
        return ceil($this->total / $this->listRow);
    }

    /**
     * 获取HTML
     * @return string
     */
    public function html()
    {
        $uri = request()->uri(true);
        // 链接没有匹配&或?，配置了伪静态也就无所谓了
        $html = '<ul>';
        for ($i = 1; $i < $this->totalPage + 1; $i++) {
            $html .= '<li><a href="' . u($uri) . '?p=' . $i . '">' . $i . '</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }
}
