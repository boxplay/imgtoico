<?php
namespace boxplay\Phpico;

class Toico
{
    public $file;
    public $size;
    public $path;
    public $name;
    public function __construct($file = false, $sizes = 32, $path = '.', $name = 'favicon')
    {
        $required_functions = array(
            'getimagesize',
            'imagecreatefromstring',
            'imagecreatetruecolor',
            'imagecolortransparent',
            'imagecolorallocatealpha',
            'imagealphablending',
            'imagesavealpha',
            'imagesx',
            'imagesy',
            'imagecopyresampled',
        );

        foreach ($required_functions as $function) {
            if (!function_exists($function)) {
                trigger_error("The PHP_ICO class was unable to find the $function function, which is part of the GD library. Ensure that the system has the GD library installed and that PHP has access to it through a PHP interface, such as PHP's GD module. Since this function was not found, the library will be unable to create ICO files.");
                return;
            }
        }
        $imginfo = getimagesize($file);
        if (!$imginfo) {
            return [
                'error' => 'not a img',
                'path' => $path . $name . '.ico',
            ];
        }
        if ($imginfo && isset($imginfo['mime'])) {
            if (!in_array($imginfo['mime'], ['image/jpeg', 'image/png', 'image/gif'])) {
                return [
                    'error' => 'img type is not supposed',
                    'path' => $path . $name . '.ico',
                ];
            }

        }
        $this->file = $file;
        $this->path = $path;
        $this->size = $sizes;
        $this->name = $name;
    }

    public function createIco()
    {
        $im = imagecreatefromjpeg($this->file) or imagecreatefrompng($this->file) or imagecreatefromgif($this->file);
        $imginfo = @getimagesize($this->file);
        $resize_im = @imagecreatetruecolor($this->size, $this->size);
        imagesavealpha($im, true);
        imagealphablending($resize_im, false); //不合并颜色,直接用$im图像颜色替换,包括透明色
        imagesavealpha($resize_im, true); //不要丢了$resize_im图像的透明色,解决生成黑色背景的问题
        imagecopyresampled($resize_im, $im, 0, 0, 0, 0, $this->size, $this->size, $imginfo[0], $imginfo[1]);
        if (imagepng($resize_im, $this->path . '/' . $this->name . '.ico', 9)) {
            imagedestroy($resize_im);
            return [
                'error' => 'ok',
                'path' => $this->path . '/' . $this->name . '.ico',
            ];
        }
        return [
            'error' => 'error',
            'path' => '',
        ];
    }
}
