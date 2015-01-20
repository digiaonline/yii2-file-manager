<?php

namespace nord\yii\filemanager\storages;

use nord\yii\filemanager\resources\ResourceInterface;

interface StorageInterface
{
    /**
     * Saves a file resource using the given configuration.
     *
     * @param ResourceInterface $resource resource instance.
     * @param array $config configuration for the operation.
     * @return boolean whether the file was saved successfully.
     */
    public function saveFile(ResourceInterface $resource, array $config = []);

    /**
     * Deletes the file with the given name.
     *
     * @param string $filename file name.
     * @return boolean whether the operation was successful.
     */
    public function deleteFile($filename);

    /**
     * Returns the URL for the file with the given name.
     *
     * @param string $filename file name.
     * @return string file URL.
     */
    public function getFileUrl($filename);

    /**
     * Returns whether the file with the given name exists.
     *
     * @param string $filename file name.
     * @return boolean the result.
     */
    public function fileExists($filename);
}