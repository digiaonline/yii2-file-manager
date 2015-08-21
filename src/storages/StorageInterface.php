<?php

namespace nord\yii\filemanager\storages;

use nord\yii\filemanager\resources\ResourceInterface;
use nord\yii\filemanager\models\File;

interface StorageInterface
{
    /**
     * Saves the given file with the specified contents
     *
     * @param File $file
     * @param string $contents
     * @return boolean whether the file was saved successfully.
     */
    public function saveFile(File $file, $contents);

    /**
     * Deletes the given file.
     *
     * @param File $file the file.
     * @return boolean whether the operation was successful.
     */
    public function deleteFile(File $file);

    /**
     * Returns the URL for the given file.
     *
     * @param File $file the file.
     * @return string file URL.
     */
    public function getFileUrl(File $file);

    /**
     * Returns the path for given file.
     *
     * @param File $file the file.
     * @return string file path.
     */
    public function getFilePath(File $file);

    /**
     * Returns whether the given file exists.
     *
     * @param File $file the file.
     * @return boolean the result.
     */
    public function fileExists(File $file);
}
