<?php

namespace system\extend;

/**
 * 水印处理类
 * @author TOP糯米
 */
class Water {
    /**
     * 错误信息
     * @var int|string|boolean
     */
    private $error = false;

    /**
     * 水印文件
     * @var [type]
     */
    private $waterPath = '';

    public function __construct() {
    }

    /**
     * 获取错误信息
     * @return int|string|boolean 错误信息
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 指定水印文件
     * @param  string $file [description]
     */
    public function waterFile($file = '') {
        $this->waterPath = $file;
    }

    /**
     * 图片合成
     * @param  string $file 处理后的文件路径
     * @param  boolean $cover 是否覆盖原始图片
     * @return string         文件名
     */
    private function addWater($file, $cover) {
        if ($this->waterPath == '') {
            $this->error = '请先调用waterFile方法来指定水印文件';
            return false;
        }
        $fileString = file_get_contents($file);
        $waterString = file_get_contents($this->waterPath);
        $image = imagecreatefromstring($fileString);
        $water = imagecreatefromstring($waterString);
        $filesize = getimagesize($file);
        list($fileWidth, $fileHeight) = $filesize;
        list($waterWidth, $waterHeight, $waterType) = getimagesize($this->waterPath);
        $positionX = $fileWidth - $waterWidth;
        $positionY = $fileHeight - $waterHeight;
        imagecopy($image, $water, $positionX, $positionY, 0, 0, $waterWidth, $waterHeight);
        // 取出原始图片信息
        $basename = basename($file);
        $dir = dirname($file) . '/';
        if (!$cover) {
            $secName = '_water';
        } else {
            $secName = '';
            @unlink($file);
        }
        $filenameArr = explode('.', $basename);
        // 原始文件后缀
        // $suffix = end($filenameArr);
        unset($filenameArr[count($filenameArr) - 1]);
        // 准备保存的文件名
        $name = implode('', $filenameArr) . $secName;
        $filename = '';
        // 按mime类型调用对应方法合成图片
        switch ($filesize['mime']) {
            case 'image/jpeg':
                $filename = $dir . $name . '.jpg';
                imagejpeg($image, $filename);
                break;
            case 'image/png':
                $filename = $dir . $name . '.png';
                imagepng($image, $filename);
                break;
            case 'image/gif':
                $filename = $dir . $name . '.gif';
                imagegif($image, $filename);
                break;
        }
        if (!$filename) {
            $this->error = '图片合成失败';
            return false;
        }
        return $filename;
    }

    /**
     * 处理图片
     * @param  string $file 处理的文件
     * @param  boolean $cover 是否覆盖原始图片，默认覆盖
     * @return boolean        成功|失败
     */
    public function handler($file = '', $cover = true) {
        if ($file == '') {
            $this->error = '请指定要处理的图片文件';
            return false;
        }
        $filename = $this->addWater($file, $cover);
        if ($filename) {
            return $filename;
        } else {
            $this->error = '水印添加失败';
            return false;
        }
    }
}