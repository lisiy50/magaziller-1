<?php

Yii::import('application.extensions.image.Image');

/**
 * Description of CImageComponent
 *
 * @author Administrator
 */
class CImageComponent extends CApplicationComponent
{
    /**
     * Drivers available:
     *  GD - The default driver, requires GD2 version >= 2.0.34 (Debian / Ubuntu users note: Some functions, eg. sharpen may not be available)
     *  ImageMagick - Windows users must specify a path to the binary. Unix versions will attempt to auto-locate.
     * @var string
     */
    public $driver = 'GD';

    public function init()
    {
        parent::init();
        if($this->driver != 'GD' && $this->driver != 'ImageMagick'){
            throw new CException('driver must be GD or ImageMagick');
        }
    }

    public function load($image) {
        return Image::factory($image, $this->driver);
    }
}
?>
