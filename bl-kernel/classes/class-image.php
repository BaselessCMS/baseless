<?php
/**
 * Image helpers
 *
 * @package    Baseless
 * @subpackage Classes
 * @category   Content
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'Baseless' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Image {

	/**
	 * Image file
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	private $image;

	/**
	 * Image width
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	private $width;

	/**
	 * Image height
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	private $height;

	/**
	 * Image resized
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    boolean
	 */
	private $imageResized;

	/**
	 * Set image
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $fileName
	 * @param  string $newWidth
	 * @param  string $newHeight
	 * @param  string $option
	 * @return void
	 */
	public function setImage( $fileName, $newWidth, $newHeight, $option = 'auto' ) {

		$this->width  = imagesx( $this->image );
		$this->height = imagesy( $this->image );

		$this->resizeImage( $newWidth, $newHeight, $option );
	}

	/**
	 * Save image
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $savePath
	 * @param  string $imageQuality
	 * @param  boolean $forceJPG
	 * @param  boolean $forceWEBP
	 * @param  boolean $forcePNG
	 * @return void
	 */
	public function saveImage( $savePath, $imageQuality = '100', $forceJPG = false, $forceWEBP = false, $forcePNG = false ) {

		$extension = strtolower( pathinfo( $savePath, PATHINFO_EXTENSION ) );

		// Remove the extension.
		$filename = substr( $savePath, 0, strrpos( $savePath, '.' ) );

		$path_complete = $filename . '.' . $extension;

		if ( $forcePNG ) {
			$extension = 'png';
		} elseif ( $forceJPG ) {
			$extension = 'jpg';
		} elseif ( $forceWEBP ) {
			$extension = 'webp';
		}

		switch ( $extension ) {
			case 'jpg':
			case 'jpeg':
				// Checking for JPG support.
				if ( imagetypes() & IMG_JPG ) {
					imagejpeg( $this->imageResized, $path_complete, $imageQuality );
				}
				break;

			case 'webp':
				// Checking for WEBP support.
				if ( imagetypes() & IMG_WEBP ) {
					imagewebp( $this->imageResized, $path_complete, $imageQuality );
				}
				break;

			case 'gif':
				// Checking for GIF support.
				if ( imagetypes() & IMG_GIF ) {
					imagegif( $this->imageResized, $path_complete );
				}
				break;

			case 'png':
				// Scale quality from 0-100 to 0-9.
				$scaleQuality = round( ( $imageQuality / 100 ) * 9 );

				// Invert quality setting as 0 is best, not 9.
				$invertScaleQuality = 9 - $scaleQuality;

				// Checking for PNG support.
				if ( imagetypes() & IMG_PNG ) {
					 imagepng( $this->imageResized, $path_complete, $invertScaleQuality );
				}
				break;

			default:
				// Fail extension detection.
				break;
		}
		imagedestroy( $this->imageResized );
	}

	/**
	 * Open image
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  string $file
	 * @return string
	 */
	private function openImage( $file ) {

		// Get extension.
		$extension = strtolower( strrchr( $file, '.' ) );

		switch( $extension ) {
			case '.jpg':
			case '.jpeg':
				$img = imagecreatefromjpeg( $file );
				break;
			case '.webp':
				$img = imagecreatefromwebp( $file );
				break;
			case '.gif':
				$img = imagecreatefromgif( $file );
				break;
			case '.png':
				$img = imagecreatefrompng( $file );
				break;
			default:
				$img = false;
				break;
		}
		return $img;
	}

	/**
	 * Resize image
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  string $newWidth
	 * @param  string $newHeight
	 * @param  mixed $option
	 * @return void
	 */
	private function resizeImage( $newWidth, $newHeight, $option ) {

		// Get optimal width and height - based on $option.
		$optionArray = $this->getDimensions( $newWidth, $newHeight, $option );

		$optimalWidth  = $optionArray['optimalWidth'];
		$optimalHeight = $optionArray['optimalHeight'];


		// Resample - create image canvas of x, y size.
		$this->imageResized = imagecreatetruecolor( $optimalWidth, $optimalHeight );
		imagealphablending( $this->imageResized, false );
		imagesavealpha( $this->imageResized, true );
		imagecopyresampled( $this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height );


		// if option is 'crop', then crop too.
		if ( 'crop' == $option ) {
			$this->crop( $optimalWidth, $optimalHeight, $newWidth, $newHeight );
		}
	}

	/**
	 * Get dimensions
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  string $newWidth
	 * @param  string $newHeight
	 * @param  mixed $option
	 * @return void
	 */
	private function getDimensions( $newWidth, $newHeight, $option ) {

		if ( ( $this->width < $newWidth ) and ( $this->height < $newHeight ) ) {
			return [
				'optimalWidth'  => $this->width,
				'optimalHeight' => $this->height
			];
		}

		switch ( $option ) {
			case 'exact':
				$optimalWidth  = $newWidth;
				$optimalHeight = $newHeight;
				break;
			case 'portrait':
				$optimalWidth  = $this->getSizeByFixedHeight( $newHeight );
				$optimalHeight = $newHeight;
				break;
			case 'landscape':
				$optimalWidth  = $newWidth;
				$optimalHeight = $this->getSizeByFixedWidth( $newWidth );
				break;
			case 'auto':
				$optionArray   = $this->getSizeByAuto( $newWidth, $newHeight );
				$optimalWidth  = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];
				break;
			case 'crop':
				$optionArray = $this->getOptimalCrop($newWidth, $newHeight);
				$optimalWidth = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];
				break;
		}

		return [
			'optimalWidth'  => $optimalWidth,
			'optimalHeight' => $optimalHeight
		];
	}

	/**
	 * Get size by width
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  string $newWidth
	 * @return string
	 */
	private function getSizeByFixedWidth( $newWidth ) {
		$ratio     = $this->height / $this->width;
		$newHeight = $newWidth * $ratio;
		return $newHeight;
	}

	/**
	 * Get size by height
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  string $newHeight
	 * @return string
	 */
	private function getSizeByFixedHeight( $newHeight ) {

		$ratio    = $this->width / $this->height;
		$newWidth = $newHeight * $ratio;
		return $newWidth;
	}

	/**
	 * Get size by auto crop
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  string $newWidth
	 * @param  string $newHeight
	 * @return array
	 */
	private function getSizeByAuto( $newWidth, $newHeight ) {

		// Image to be resized is wider (landscape).
		if ( $this->height < $this->width ) {
			$optimalWidth  = $newWidth;
			$optimalHeight = $this->getSizeByFixedWidth( $newWidth );

		// Image to be resized is taller (portrait).
		} elseif ( $this->height > $this->width ) {
			$optimalWidth  = $this->getSizeByFixedHeight( $newHeight );
			$optimalHeight = $newHeight;

		// Image to be resized is a square.
		} else {
			if ( $newHeight < $newWidth ) {
				$optimalWidth = $newWidth;
				$optimalHeight= $this->getSizeByFixedWidth( $newWidth );
			} elseif ( $newHeight > $newWidth ) {
				$optimalWidth = $this->getSizeByFixedHeight( $newHeight );
				$optimalHeight= $newHeight;

			// Square being resized to a square.
			} else {
				$optimalWidth  = $newWidth;
				$optimalHeight = $newHeight;
			}
		}
		return [
			'optimalWidth'  => $optimalWidth,
			'optimalHeight' => $optimalHeight
		];
	}

	/**
	 * Get optimal crop
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  string $newWidth
	 * @param  string $newHeight
	 * @return array
	 */
	private function getOptimalCrop( $newWidth, $newHeight ) {

		$heightRatio = $this->height / $newHeight;
		$widthRatio  = $this->width / $newWidth;

		if ( $heightRatio < $widthRatio ) {
			$optimalRatio = $heightRatio;
		} else {
			$optimalRatio = $widthRatio;
		}

		$optimalHeight = $this->height / $optimalRatio;
		$optimalWidth  = $this->width  / $optimalRatio;

		return [
			'optimalWidth'  => $optimalWidth,
			'optimalHeight' => $optimalHeight
		];
	}

	/**
	 * Crop image
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  string $optimalWidth
	 * @param  string $optimalHeight
	 * @param  string $newWidth
	 * @param  string $newHeight
	 * @return void
	 */
	private function crop( $optimalWidth, $optimalHeight, $newWidth, $newHeight ) {

		// Find center - this will be used for the crop.
		$cropStartX = ( $optimalWidth / 2 ) - ( $newWidth / 2 );
		$cropStartY = ( $optimalHeight / 2 ) - ( $newHeight / 2 );

		$crop = $this->imageResized;
		// imagedestroy( $this->imageResized) ;

		// Now crop from center to exact requested size.
		$this->imageResized = imagecreatetruecolor( $newWidth , $newHeight );
		imagealphablending( $this->imageResized, false );
		imagesavealpha( $this->imageResized, true );
		imagecopyresampled( $this->imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight );
	}
}
