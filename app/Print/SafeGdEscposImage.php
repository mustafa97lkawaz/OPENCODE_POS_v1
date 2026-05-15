<?php

namespace App\Print;

use Exception;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\GdEscposImage;

/**
 * PHP 8 safe GD image loader for ESC/POS.
 *
 * mike42/escpos-php v2.2's GdEscposImage::readImageFromGdResource() only checks
 * is_resource($im) — on PHP 8 the GD functions return \GdImage objects, not
 * resources, so it wrongly throws "Failed to load image." The project worked
 * around this by hand-editing the vendor file, which `composer install` wipes.
 *
 * This subclass fully reimplements the method (it deliberately does NOT call
 * parent:: for the type check, so it stays correct even after the vendor file
 * is reverted to the upstream original).
 */
class SafeGdEscposImage extends GdEscposImage
{
    public function readImageFromGdResource($im)
    {
        if (!is_resource($im) && !($im instanceof \GdImage)) {
            throw new Exception("Failed to load image.");
        } elseif (!EscposImage::isGdLoaded()) {
            throw new Exception(__FUNCTION__ . " requires 'gd' extension.");
        }
        /* Make a string of 1's and 0's */
        $imgHeight = imagesy($im);
        $imgWidth = imagesx($im);
        $imgData = str_repeat("\0", $imgHeight * $imgWidth);
        for ($y = 0; $y < $imgHeight; $y++) {
            for ($x = 0; $x < $imgWidth; $x++) {
                /* Faster to average channels, blend alpha and negate the image here than via filters (tested!) */
                $cols = imagecolorsforindex($im, imagecolorat($im, $x, $y));
                // 1 for white, 0 for black, ignoring transparency
                $greyness = (int)(($cols['red'] + $cols['green'] + $cols['blue']) / 3) >> 7;
                // 1 for black, 0 for white, taking into account transparency
                $black = (1 - $greyness) >> ($cols['alpha'] >> 6);
                $imgData[$y * $imgWidth + $x] = $black;
            }
        }
        $this->setImgWidth($imgWidth);
        $this->setImgHeight($imgHeight);
        $this->setImgData($imgData);
    }
}
