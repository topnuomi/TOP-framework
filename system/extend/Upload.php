<?php
/**
 * @author TOP糯米 2017
 */

namespace system\extend;

use system\library\Load;

/**
 * 文件上传类
 * @author TOP糯米
 */
class Upload {
    private static $instance;
    private static $fileType;
    private static $dirName;
    private $image;
    private $error;

    private function __construct() {
    }

    /**
     * 静态调用时传入保存目录以及文件类型
     * @param string $dirName
     * @param string $fileType
     * @return \system\extend\Upload
     */
    public static function init($dirName, $fileType = '') {
        if (!self::$instance) {
            self::$instance = new self();
        }
        self::$dirName = $dirName;
        self::$fileType = ($fileType) ? $fileType : ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF'];
        return self::$instance;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    public function uploadPicture($fileName = '', $width = 0, $height = 0, $waterFile = '') {
        if (!empty($_FILES)) {
            $data = [];
            $picture = Load::model('Picture');
            foreach ($_FILES as $k => $v) {
                $fileParts = pathinfo($v['name']);
                if (in_array($fileParts['extension'], self::$fileType)) {
                    if (!is_dir(self::$dirName))
                        mkdir(self::$dirName, 0777, true);
                    $targetFile = rtrim(self::$dirName, '/') . '/' . ((!$fileName) ? md5($v['name'] . uniqid()) : $fileName);
                    $filePath = $targetFile . '.' . $fileParts['extension'];
                    $tempFile = $v['tmp_name'];
                    // $type = getimagesize($tempFile)['mime'];
                    if (move_uploaded_file($tempFile, $filePath)) {
                        if ($width && $height) {
                            $filePath = resize_image($filePath, $width, $height);
                        }
                        $hash = md5_file($filePath);
                        $file = $picture->getPictureByHash($hash);
                        if ($file) {
                            @unlink($filePath);
                            $filePath = $file['path'];
                        } else {
                            if ($waterFile) {
                                $water = new Water();
                                $water->waterFile($waterFile);
                                $filePath = $water->handler($filePath);
                            }
                            $picture->insert([
                                'path' => ltrim($filePath, '.'),
                                'hash' => $hash
                            ]);
                        }
                        $filePath = ltrim($filePath, '.');
                        $data[] = $filePath;
                    } else {
                        $this->error = '上传失败';
                        return false;
                    }
                } else {
                    $this->error = '文件类型不被允许';
                    return false;
                }
            }
            return $data;
        }
        $this->error = '请上传文件';
        return false;
    }
}