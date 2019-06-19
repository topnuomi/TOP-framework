<?php

namespace system\extend;

use top\library\Register;

/**
 * 分页类
 * @author topnuomi 2018年11月28日
 */
class Page
{

    // 每页显示记录数
    public $listRow;

    // 记录总数
    private $total;

    // 总页数
    private $totalPage;

    // 当前页码
    public $page;

    public $firstRow;

    public function __construct($listRow, $total)
    {
        $this->listRow = $listRow;
        $this->total = $total;
        $this->page = (isset($_GET['p']) && $_GET['p']) ? (int)$_GET['p'] : ((isset($_POST['p']) && $_POST['p']) ? (int)$_POST['p'] : 1);
    }

    private function firstRow()
    {
        return ($this->page - 1) * $this->listRow;
    }

    private function totalPage()
    {
        return ceil($this->total / $this->listRow);
    }

    public function process()
    {
        $this->totalPage = $this->totalPage();
        $this->firstRow = $this->firstRow();
        return $this;
    }

    public function html()
    {
        $url = Register::get('Route')->rawUri;
        // 链接没有匹配&或?，配置了伪静态也就无所谓了
        $html = '<ul>';
        for ($i = 1; $i < $this->totalPage + 1; $i++) {
            $html .= '<li><a href="' . u($url) . '?p=' . $i . '">' . $i . '</a></li>';
        }
        $html .= '</ul>';
        return $html;
    }
}
