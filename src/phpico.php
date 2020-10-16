<?php
namespace boxplay\phpico;

class PHP_ICO
{
    /**
     * Images in the BMP format.
     *
     * @var array
     * @access private
     */
    public $_images = array();

    /**
     * Flag to tell if the required functions exist.
     *
     * @var boolean
     * @access private
     */
    public $_has_requirements = false;

    /**
     * Constructor - Create a new ICO generator.
     *
     * If the constructor is not passed a file, a file will need to be supplied using the {@link PHP_ICO::add_image}
     * function in order to generate an ICO file.
     *
     * @param string $file Optional. Path to the source image file.
     * @param array $sizes Optional. An array of sizes (each size is an array with a width and height) that the source image should be rendered at in the generated ICO file. If sizes are not supplied, the size of the source image will be used.
     */
    public function __construct($file = false, $sizes = array())
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

        $this->_has_requirements = true;
        $this->createMap($file);
    }

    public function createIco($file, $path = '.')
    {
        $im = imagecreatefromjpeg($file) or imagecreatefrompng($file) or imagecreatefromgif($file);
        $imginfo = @getimagesize($file);
        $resize_im = @imagecreatetruecolor(32, 32);
        $size = 32;
        imagesavealpha($im, true);
        imagealphablending($resize_im, false); //不合并颜色,直接用$im图像颜色替换,包括透明色
        imagesavealpha($resize_im, true); //不要丢了$resize_im图像的透明色,解决生成黑色背景的问题
        imagecopyresampled($resize_im, $im, 0, 0, 0, 0, $size, $size, $imginfo[0], $imginfo[1]);
        imagepng($resize_im, $path . '/timg.ico', 9);
    }
}
