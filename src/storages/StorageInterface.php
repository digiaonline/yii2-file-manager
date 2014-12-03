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
     * @param string $name file name.
     * @return boolean whether the operation was successful.
     */
    public function deleteFile($name);

    /**
     * Returns the URL for the file with the given name.
     *
     * @param string $name file name.
     * @return string file URL.
     */
    public function getFileUrl($name);

    /**
     * Returns whether the file with the given name exists.
     *
     * @param string $name file name.
     * @return boolean the result.
     */
    public function fileExists($name);
}