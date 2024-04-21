<?php
/**
 * File system helpers
 *
 * @package    JSON CMS
 * @subpackage Classes
 * @category   Core
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'JSON_CMS' ) ) {
	die( 'You are not allowed to access this file directly.' );
}

class Filesystem {

	/**
	 * Directories list
	 *
	 * Returns an array with the absolutes directories.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $path
	 * @param  string $regex
	 * @param  boolean $sortByDate
	 * @return void
	 */
	public static function listDirectories( $path, $regex = '*', $sortByDate = false ) {

		$directories = glob( $path.$regex, GLOB_ONLYDIR );

		if ( empty( $directories ) ) {
			return [];
		}

		if ( $sortByDate ) {
			usort( $directories, function( $a, $b ) {
				      return filemtime( $b ) - filemtime( $a );
			      }
			);
		}
		return $directories;
	}

	/**
	 * Files list
	 *
	 * Returns an array with the list of files with the absolute path.
	 * $sortByDate = TRUE, the first file is the newer file.
	 * $chunk = amount of chunks, FALSE if you don't want to chunk.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $path
	 * @param  string $regex
	 * @param  string $extension
	 * @param  boolean $sortByDate
	 * @param  boolean $chunk
	 * @return void
	 */
	public static function listFiles( $path, $regex = '*', $extension = '*', $sortByDate = false, $chunk = false ) {

		error_log( $path . $regex . '.' . $extension );
		$files = glob( $path.$regex . '.' . $extension );

		if ( empty( $files ) ) {
			return [];
		}

		if ( $sortByDate ) {
			usort( $files, function( $a, $b ) {
					return filemtime( $b ) - filemtime( $a );
				}
			);
		}

		// Split the list of files into chunks.
		// @link http://php.net/manual/en/function.array-chunk.php
		if ( $chunk ) {
			return array_chunk( $files, $chunk );
		}
		return $files;
	}

	/**
	 * Make directory
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $pathname
	 * @param  boolean $recursive
	 * @return void
	 */
	public static function mkdir( $pathname, $recursive = false ) {
		return mkdir( $pathname, DIR_PERMISSIONS, $recursive );
	}

	/**
	 * Remove directory
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $pathname
	 * @return void
	 */
	public static function rmdir( $pathname ) {
		\Log :: set( 'rmdir = ' . $pathname, LOG_TYPE_INFO );
		return rmdir( $pathname );
	}

	/**
	 * Move file
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $oldname
	 * @param  string $newname
	 * @return void
	 */
	public static function mv( $oldname, $newname ) {
		\Log :: set( 'mv ' . $oldname . ' ' . $newname, LOG_TYPE_INFO );
		return rename( $oldname, $newname );
	}

	/**
	 * Remove file
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $filename
	 * @return void
	 */
	public static function rmfile( $filename ) {
		\Log :: set( 'rmfile = ' . $filename, LOG_TYPE_INFO );
		return unlink( $filename );
	}

	/**
	 * File exists
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $filename
	 * @return boolean
	 */
	public static function fileExists( $filename ) {
		return file_exists( $filename );
	}

	/**
	 * Directory exists
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $path
	 * @return boolean
	 */
	public static function directoryExists( $path ) {
		return file_exists( $path );
	}

	/**
	 * Copy recursive
	 *
	 * Copy recursive a directory to another.
	 * $source = /home/diego/example or /home/diego/example/
	 * $destination = /home/diego/newplace or /home/diego/newplace/
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $source
	 * @param  string $destination
	 * @param  boolean $skipDirectory
	 * @return boolean
	 */
	public static function copyRecursive( $source, $destination, $skipDirectory = false ) {

		$source      = rtrim( $source, DS );
		$destination = rtrim( $destination, DS );

		// Check $source directory if exists.
		if ( ! self :: directoryExists( $source ) ) {
			return false;
		}

		// Check $destination directory if exists.
		if ( ! self :: directoryExists( $destination ) ) {

			// Create the $destination directory.
			if ( ! mkdir( $destination, DIR_PERMISSIONS, true ) ) {
				return false;
			}
		}

		foreach ( $iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator( $source, \RecursiveDirectoryIterator :: SKIP_DOTS ), \RecursiveIteratorIterator :: SELF_FIRST ) as $item ) {

			$currentDirectory = dirname($item->getPathName() );
			if ( $skipDirectory !== $currentDirectory ) {

				if ( $item->isDir() ) {
					@mkdir( $destination . DS . $iterator->getSubPathName() );
				} else {
					copy( $item, $destination . DS . $iterator->getSubPathName() );
				}
			}
		}
		return true;
	}

	/**
	 * Delete recursive
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $source
	 * @param  boolean $deleteDirectory
	 * @return boolean
	 */
	public static function deleteRecursive( $source, $deleteDirectory = true ) {

		\Log :: set( 'deleteRecursive = ' . $source, LOG_TYPE_INFO );

		if ( ! self :: directoryExists( $source ) ) {
			return false;
		}

		foreach ( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $source, \RecursiveIteratorIterator :: CHILD_FIRST ) ) as $item ) {

			if ( $item->isFile() || $item->isLink() ) {
				unlink( $item );
			} else {
				rmdir( $item );
			}
		}

		if ( $deleteDirectory ) {
			return rmdir( $source );
		}
		return true;
	}

	/**
	 * Zip file or directory
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $source
	 * @param  string $destination
	 * @return boolean
	 */
	public static function zip( $source, $destination ) {

		if ( ! extension_loaded( 'zip' ) ) {
			return false;
		}

		if ( ! file_exists( $source ) ) {
			return false;
		}

		$zip = new \ZipArchive();
		if ( ! $zip->open( $destination, \ZIPARCHIVE :: CREATE ) ) {
			return false;
		}

		if ( true === is_dir( $source ) ) {

			$iterator = new \RecursiveDirectoryIterator( $source );
			$iterator->setFlags( \RecursiveDirectoryIterator :: SKIP_DOTS );
			$files = new \RecursiveIteratorIterator( $iterator, \RecursiveIteratorIterator :: SELF_FIRST );

			foreach ( $files as $file ) {
				$file = realpath( $file );
				if (is_dir( $file ) ) {
					$zip->addEmptyDir( ltrim( str_replace( $source, '', $file ), "/\\" ) );
				} elseif ( is_file( $file ) ) {
					$zip->addFromString( ltrim( str_replace( $source, '', $file ), "/\\" ), file_get_contents( $file ) );
				}
			}
		} elseif ( is_file( $source ) ) {
			$zip->addFromString( basename( $source ), file_get_contents( $source ) );
		}
		return $zip->close();
	}

	/**
	 * Unzip file or directory
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $source
	 * @param  string $destination
	 * @return boolean
	 */
	public static function unzip( $source, $destination ) {

		if ( ! extension_loaded( 'zip' ) ) {
			return false;
		}

		if ( ! file_exists( $source ) ) {
			return false;
		}

		$zip = new \ZipArchive();
		if ( ! $zip->open( $source ) ) {
			return false;
		}

		$zip->extractTo( $destination );
		return $zip->close();
	}

	/**
	 * Next filename
	 *
	 * Returns the next filename if the filename already exists,
	 * otherwise returns the original filename.
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $filename
	 * @param  string $path
	 * @return string
	 */
	public static function nextFilename( $filename, $path = PATH_UPLOADS ) {

		// Clean filename and get extension.
		$fileExtension = pathinfo( $filename, PATHINFO_EXTENSION );
		$fileExtension = \Text :: lowercase( $fileExtension );
		$filename      = pathinfo( $filename, PATHINFO_FILENAME );
		$filename      = \Text :: removeSpaces( $filename );
		$filename      = \Text :: removeQuotes( $filename );

		// Search for the next filename.
		$tmpName = $filename . '.' . $fileExtension;
		if ( \Sanitize :: pathFile( $path.$tmpName ) ) {
			$number  = 0;
			$tmpName = $filename . '_' . $number . '.' . $fileExtension;
			while ( \Sanitize :: pathFile( $path . $tmpName ) ) {
				$number  = $number + 1;
				$tmpName = $filename . '_' . $number . '.' . $fileExtension;
			}
		}
		return $tmpName;
	}

	/**
	 * Filename
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $file
	 * @return string
	 */
	public static function filename( $file ) {
		return basename( $file );
	}

	/**
	 * File extension
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $file
	 * @return string
	 */
	public static function extension( $file ) {
		return pathinfo( $file, PATHINFO_EXTENSION );
	}

	/**
	 * Get file or directory size
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $fileOrDirectory
	 * @return mixed
	 */
	public static function getSize( $fileOrDirectory ) {

		// Files.
		if ( is_file( $fileOrDirectory ) ) {
			return filesize( $fileOrDirectory );
		}
		// Directories.
		if ( file_exists( $fileOrDirectory ) ) {

		    $size = 0;
		    foreach ( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $fileOrDirectory, FilesystemIterator :: SKIP_DOTS ) ) as $file ) {
				try {
					$size += $file->getSize();
				} catch ( \Exception $e ) {
					// SplFileInfo :: getSize RuntimeException will be thrown on broken symlinks/errors.
				}
		    }
		    return $size;
		}
		return false;
	}

	/**
	 * Human readable file size
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  integer $bytes
	 * @param  integer $decimals
	 * @return string
	 */
	public static function bytesToHumanFileSize( $bytes, $decimals = 2 ) {

	    $size   = [ 'B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB','YB' ];
	    $factor = floor( ( strlen( $bytes ) - 1 ) / 3 );

	    return sprintf(
			"%.{$decimals}f ",
			$bytes / pow( 1024, $factor )
		) . @$size[$factor];
	}

	/**
	 * MIME types
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $file
	 * @return string
	 */
	public static function mimeType( $file ) {

		if ( function_exists( 'mime_content_type' ) ) {
			return mime_content_type( $file );
		}

		if ( function_exists( 'finfo_file' ) ) {
			$fileinfo = finfo_open( FILEINFO_MIME_TYPE );
			$mimeType = finfo_file( $fileinfo, $file );
			finfo_close( $fileinfo );

			return $mimeType;
		}
		return false;
	}

	/**
	 * Symlink
	 *
	 * @since  1.0.0
	 * @access public
	 * @access static
	 * @param  string $from
	 * @param  string $to
	 * @return void
	 */
	public static function symlink( $from, $to ) {

		if ( function_exists( 'symlink' ) ) {
			return symlink( $from, $to );
		} else {
			return copy( $from, $to );
		}
	}
}
