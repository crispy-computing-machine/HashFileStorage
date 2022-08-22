<?php
namespace CoreLibrarys;
/**
 * Class HashFileStorage
 *
 * Hash table / hash bucket / hash map
 *
 * Folder file storage limits:.
 *
 *  Windows FAT32 = 65,534
 *  Windows NTFS = 4,294,967,295 (unlimited)
 *  Max files using hash storage:
 *  one hundred and thirty-seven billion,
 *  four hundred and thirty-eight million,
 *  nine hundred and fifty-three thousand,
 *  four hundred and forty
 *
 *  Linux (uname -r to find ext):
 *  ext2/ext3 = 10,000
 *  ext4 = unlimited
 *
 * // Test storage of multi files in multi folders
 * $oStorage = new HashFileStorage();
 * for($i = 0; $i <= 1000; $i++){
 * $sTestFilename = uniqid("store_");
 * $sTestFileText = "This file contains text for file: " . $sTestFilename;
 * $bSave = $oStorage->file_put_contents($sTestFilename, $sTestFileText);
 * error_log("Saving HashFileStorage file: " . $sTestFilename . " (" . var_export($bSave, TRUE) . ")");
 * }
 */
class HashFileStorage
{
    /**
     * 16 should suffice in most cases
     */
    const DEFAULT_SUBDIRECTORY_LEVELS = 16;

    /**
     * base directory to store files
     *
     * @var string $storageDirectory
     */
    private $storageDirectory;

    /**
     * Number of sub directories to create.
     *
     * @var int $subdirectoryLevels
     */
    private $subdirectoryLevels;

    /**
     * HashFileStorage constructor.
     *
     * @param string $sStoragePath        e.g. /storage/11-10-2016/ <- trailing slash
     * @param int    $iSubdirectoryLevels
     */
    public function __construct($sStoragePath = null, $iSubdirectoryLevels = self::DEFAULT_SUBDIRECTORY_LEVELS)
    {
        try {
            $this->setBaseDirectory($sStoragePath);
        } catch (ErrorException $e) {
            error_log($e);
        }
        $this->setSubdirectoryLevels($iSubdirectoryLevels);
    }

    /**
     * Wrapper of file_put_contents but store in dir using $sHashFilename to build sub dirs path.
     *
     * @param string $sFilename
     * @param string $sHashFileContent
     * @param int    $flags - FILE_APPEND | LOCK_EX
     *
     * @return bool
     */
    public function file_put_contents($sFilename, $sHashFileContent, $flags = 0)
    {
        return file_put_contents( $this->getFilePath($sFilename) . $sFilename, $sHashFileContent, $flags);

    }

    /**
     * Wrapper for file_get_contents but get in dir using $sHashFilename to build sub dirs path.
     *
     * @param string $sFilename
     *
     * @return string
     */
    public function file_get_contents($sFilename)
    {
        return file_get_contents($this->getFilePath($sFilename) . $sFilename);
    }

    /**
     * Set file storage base directory
     *
     * @param string $sStoragePath
     * @return string
     */
    public function setBaseDirectory($sStoragePath = PATH_DATA)
    {
        // passing in or default storage location
        $this->storageDirectory = $sStoragePath;

        // Ensure trailing slash
        $this->storageDirectory = $this->trailingSlash($this->storageDirectory);

        // Create directory
        $this->mkDir($this->storageDirectory);

        // return it for info
        return $this->storageDirectory;
    }

    /**
     * Levels of directories to use, 16 should suffice in most cases.
     *
     * @param int $levels
     */
    public function setSubdirectoryLevels($levels = self::DEFAULT_SUBDIRECTORY_LEVELS)
    {
        $this->subdirectoryLevels = ($levels - 1); // minus 1 for zero based array
    }

    /**
     * Build directory structure from filename + base directory.
     *
     * @param $filename
     * @return string
     */
    public function getFilePath($filename)
    {
        // Hash of filename
        $hash = md5($filename);

        // Build path using hash character array 1-32
        $path = $this->storageDirectory;
        for ($i = 0; $i <= $this->subdirectoryLevels; ++$i){
            $path .= $hash[$i] . DIRECTORY_SEPARATOR;
            $this->mkDir($path);
        }


        return $path;
    }

    /**
     * Mkdir wrapper
     *
     * @param $path
     * @return bool
     */
    function mkDir($path){
        return !(!@mkdir($path) && !@is_dir($path));
    }

    /**
     * Helper: Appends a trailing slash.
     *
     * @param string $string what to add the trailing slash to
     *
     * @return string string with trailing slash added
     */
    public function trailingSlash($string)
    {
        return $this->untrailingSlash($string) . DIRECTORY_SEPARATOR;
    }

    /**
     * Helper: Removes trailing forward slashes and backslashes if they exist.
     *
     * @param string $string what to remove the trailing slashes from
     *
     * @return string string without the trailing slashes
     */
    public function untrailingSlash($string)
    {
        return rtrim($string, DIRECTORY_SEPARATOR);
    }
}
