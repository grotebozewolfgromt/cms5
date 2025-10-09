<?php
/**
 * In this library exist only image related functions
 *
 * IMPORTANT:
 * This library is language independant, so don't use language specific element
 * 
 * @author Dennis Renirie*
 */

//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_inet.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');

/**
 * get the height of an jpeg
 * @param string $sImagePath
 * @return int
 */
function getHeightJPG($sImagePath)
{
        $iResult = 0;

        $src_img = ImageCreateFromJpeg($sImagePath);
        $iResult = ImageSY($src_img);
        ImageDestroy($src_img);

        return $iResult;
}

/**
 * get the width of an jpeg
 * @param <type> $sImagePath
 * @return <type>
 */

function getWidthJPG($sImagePath)
{
        $iResult = 0;

        $src_img = ImageCreateFromJpeg($sImagePath);
        $iResult = ImageSX($src_img);
        ImageDestroy($src_img);

        return $iResult;
}


/**
 * resizen van plaatjes als resizeJPG(), maar als je ALTIJD resized worden kleine plaatjes zo lelijk
 *
 * @uses resizeJPG
 *
 * @param string $sImagePath path van het plaatje dus bijv. /var/www/plaatje.jpg
 * @param int $iMaxWidth
 * @param int $iMaxHeight
 * @param int $iImageQualityPercent percentage kwaliteit bijv 85%
 * @return bool resized ?
 */
function checkSizeAndResize($sImagePath, $iMaxWidth, $iMaxHeight, $iImageQualityPercent)
{
        $bResized = false;
        // Set a few variables
        //$image = "/home/web/images/original.jpg";
        //$newimage = "/home/web/images/new.jpg";
        //$image_quality = 80;
        //$addborder = 1;
        //$max_height = 200;
        //$max_width = 300;
        $imgSrc = ImageCreateFromJpeg($sImagePath);
        $iWidth = ImageSX($imgSrc);
        $iHeight = ImageSY($imgSrc);
        ImageDestroy($imgSrc);

        if (($iWidth > $iMaxWidth) || ($iHeight > $iMaxHeight)) //als buiten maximale waarden dan resizen
        {
                resizeJPG($sImagePath, $sImagePath, $iImageQualityPercent, 0, $iMaxHeight, $iMaxWidth);
                $bResized = true;
        }

        return($bResized);
}

/**
 * het resizen van een jpg
 * 
 * @author liquidkernel
 * 
 * @param string $sOriginalImage
 * @param string $sNewImage
 * @param int $iImageQualityPercent
 * @param bool $bAddBorder
 * @param int $iMaxHeight
 * @param int $iMaxWidth
 */
function resizeJPG($sOriginalImage, $sNewImage, $iImageQualityPercent, $bAddBorder, $iMaxHeight, $iMaxWidth)
{
        // Set a few variables
        //$image = "/home/web/images/original.jpg";
        //$newimage = "/home/web/images/new.jpg";
        //$image_quality = 80;
        //$addborder = 1;
        //$max_height = 200;
        //$max_width = 300;

        // Main code
        $src_img = ImageCreateFromJpeg($sOriginalImage);
        $orig_x = ImageSX($src_img);
        $orig_y = ImageSY($src_img);

        $new_y = $iMaxHeight;
        $new_x = $orig_x/($orig_y/$iMaxHeight);

        if ($new_x > $iMaxWidth) {
                $new_x = $iMaxWidth;
                $new_y = $orig_y/($orig_x/$iMaxWidth);
        }

        $dst_img = ImageCreateTrueColor($new_x,$new_y);
        ImageCopyResampled($dst_img, $src_img, 0, 0, 0, 0, $new_x, $new_y, $orig_x, $orig_y);

        if ($bAddBorder == 1) {
                // Add border
                $black = ImageColorAllocate($dst_img, 0, 0, 0);
                ImageSetThickness($dst_img, 1);
                ImageLine($dst_img, 0, 0, $new_x, 0, $black);
                ImageLine($dst_img, 0, 0, 0, $new_y, $black);
                ImageLine($dst_img, $new_x-1, 0, $new_x-1, $new_y, $black);
                ImageLine($dst_img, 0, $new_y-1, $new_x, $new_y-1, $black);
        }

        ImageJpeg($dst_img, $sNewImage, $iImageQualityPercent);
        ImageDestroy($src_img);
        ImageDestroy($dst_img);
}

?>
