<?php

namespace nord\yii\filemanager\storages;

use nord\yii\filemanager\resources\ResourceInterface;
use nord\yii\filemanager\models\File;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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
            $this->setBasePath(Yii::getAlias('@app/web') . '/' . self::DEFAULT_DIRECTORY);
        }
        if (!isset($this->_baseUrl) && Yii::$app instanceof \yii\web\Application) {
            $this->setBaseUrl(Yii::$app->request->baseUrl . DIRECTORY_SEPARATOR . self::DEFAULT_DIRECTORY);
        }
    }

    /**
     * @inheritdoc
     */
    public function saveFile(File $file, $contents)
    {
        $path = $this->getBasePath().'/'.$file->getFilePath();
        @mkdir(dirname($path), 0777, true);

        return file_put_contents($path, $contents) !== false;
    }

    /**
     * @inheritdoc
     */
    public function deleteFile(File $file)
    {
        if (!$this->fileExists($file)) {
            throw new Exception("Failed to locate file to delete '{$file->getFileName()}'.");
        }
        return unlink($this->getFilePath($file));
    }

    /**
     * @inheritdoc
     */
    public function getFileUrl(File $file)
    {
        $url = $this->getBaseUrl() . '/' . $file->getFilePath();
        return Url::to($url, true);
    }

    /**
     * Returns the file path for the file with the given name.
     *
     * @param string $filename file name.
     * @return string file path.
     */
    public function getFilePath(File $file)
    {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . $file->getFilePath();
    }

    /**
     * @inheritdoc
     */
    public function fileExists(File $file)
    {
        return file_exists($this->getFilePath($file));
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
