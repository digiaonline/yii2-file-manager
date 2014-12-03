<?php

namespace nord\yii\filemanager\storages;

use nord\yii\filemanager\resources\ResourceInterface;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

class FileStorage extends Component implements StorageInterface
{
    // Name of the default file directory.
    const DEFAULT_DIRECTORY = 'files';

    private $_basePath;
    private $_baseUrl;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!isset($this->_basePath)) {
            $this->setBasePath(Yii::getAlias('@webroot') . '/' . self::DEFAULT_DIRECTORY);
        }
        if (!isset($this->_baseUrl)) {
            $this->setBaseUrl(Yii::$app->request->baseUrl . DIRECTORY_SEPARATOR . self::DEFAULT_DIRECTORY);
        }
    }

    /**
     * @inheritdoc
     */
    public function saveFile(ResourceInterface $resource, array $config = [])
    {
        if (!isset($config['filename'])) {
            throw new InvalidParamException('Trying to save a file without a filename.');
        }
        $path = $this->getBasePath() . DIRECTORY_SEPARATOR . $config['filename'];
        @mkdir(dirname($path), 0777, true);
        $resourceConfig = ArrayHelper::remove($config, 'resource', []);
        return $resource->saveAs($path, $resourceConfig);
    }

    /**
     * @inheritdoc
     */
    public function deleteFile($name)
    {
        if (!$this->fileExists($name)) {
            throw new Exception("Failed to locate file to delete '$name'.");
        }
        return unlink($this->getFilePath($name));
    }

    /**
     * @inheritdoc
     */
    public function getFileUrl($name)
    {
        return $this->getBaseUrl() . '/' . $name;
    }

    /**
     * @inheritdoc
     */
    public function fileExists($name)
    {
        return file_exists($this->getFilePath($name));
    }

    /**
     * Returns the file path for the file with the given name.
     *
     * @param string $name file name.
     * @return string file path.
     */
    public function getFilePath($name)
    {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * Returns the base path used when saving files.
     *
     * @return string base path.
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * Sets the base path for saving files.
     *
     * @param string $basePath base path.
     */
    public function setBasePath($basePath)
    {
        $this->_basePath = rtrim($basePath, '/\\');
    }

    /**
     * Returns the base URL for accessing files.
     *
     * @return string base URL.
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * Sets the base URL for accessing files.
     *
     * @param string $baseUrl base URL.
     */
    public function setBaseUrl($baseUrl)
    {
        $this->_baseUrl = rtrim($baseUrl, '/');
    }
}