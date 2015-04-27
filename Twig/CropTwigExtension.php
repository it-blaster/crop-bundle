<?php

namespace Fenrizbes\CropBundle\Twig;

use Fenrizbes\UploadableBundle\File\UploadableFile;
use Symfony\Component\HttpFoundation\File\File;

class CropTwigExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    protected $root_path;

    public function __construct($root_path)
    {
        $this->root_path = $root_path;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'CropTwigExtension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('crop', array($this, 'crop'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('image_size', array($this, 'getSize'))
        );
    }

    /**
     * Crops a file and returns crop's web path
     *
     * @param array $croppable
     * @param int $index
     * @return mixed
     */
    public function crop(array $croppable, $index = 0)
    {
        $coordinates = $croppable['coordinates'][$index];

        $file = new UploadableFile($this->root_path, $croppable['image']);
        $name = $this->makeCropName($file, $coordinates);
        $path = $file->getRootPath() . $name;

        if (!file_exists($path)) {
            $this->doCrop($file, $coordinates, $path);
        }

        return $name;
    }

    /**
     * Returns the name for a cropped file
     *
     * @param UploadableFile $file
     * @param $coordinates
     * @return mixed
     */
    protected function makeCropName(UploadableFile $file, $coordinates)
    {
        $ext    = $file->getExtension();
        $suffix = implode('_', $coordinates);

        return preg_replace('/\.'. $ext .'$/ui', '_crop_'. $suffix .'.'. $ext, $file->getWebPath());
    }

    /**
     * Crops a file
     *
     * @param UploadableFile $file
     * @param $coordinates
     * @param $path
     */
    protected function doCrop(UploadableFile $file, $coordinates, $path)
    {
        $crop = imagecreatetruecolor($coordinates['min_width'], $coordinates['min_height']);

        switch ($file->getExtension()) {
            case 'gif':
                $source = imagecreatefromgif($file); break;

            case 'png':
                $source = imagecreatefrompng($file); break;

            default:
                $source = imagecreatefromjpeg($file);
        }

        if (preg_match('/^(gif|png)$/', $file->getExtension())) {
            imagecolortransparent($crop, imagecolorallocatealpha($crop, 0, 0, 0, 127));
            imagealphablending($crop, false);
            imagesavealpha($crop, true);
        }

        imagecopyresampled(
            $crop, $source,
            0, 0,
            $coordinates['left'], $coordinates['top'],
            $coordinates['min_width'], $coordinates['min_height'],
            $coordinates['width'], $coordinates['height']
        );

        switch ($file->getExtension()) {
            case 'gif':
                imagegif($crop, $path); break;

            case 'png':
                imagepng($crop, $path); break;

            default:
                imagejpeg($crop, $path, 100);
        }
    }

    /**
     * Returns image size info
     *
     * @param $image
     * @param bool $relative
     * @return array
     */
    public function getSize($image, $relative = false)
    {
        if (!$image instanceof File) {
            if ($relative) {
                $image = $this->root_path . $image;
            }

            $image = new File($image);
        }

        return getimagesize($image->getRealPath());
    }
}