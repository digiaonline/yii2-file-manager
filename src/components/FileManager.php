<?php

namespace nord\yii\filemanager\components;

use nord\yii\filemanager\resources\ResourceInterface;
use nord\yii\filemanager\storages\FileStorage;
use nord\yii\filemanager\storages\StorageInterface;
use nord\yii\filemanager\models\File;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

class FileManager extends Component
{
    // Canonical component ID for this component.
    const COMPONENT_ID = 'fileManager';

    // Name of the default storage.
    const DEFAULT_STORAGE = 'file';

    /**
     * @var array configuration of the enabled storage components that can be used for storing files.
     */
    public $storages = [];

    /**
     * @var string the default storage to use when saving files
     */
    public $defaultStorage = self::DEFAULT_STORAGE;

    /**
     * @var string name of the file model class.
     */
    public $modelClass;

    private $_storages;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!isset($this->modelClass)) {
            $this->modelClass = File::className();
        }

        $this->initStorages();
    }

    /**
     * Initializes the storage components for this component.
     */
    protected function initStorages()
    {
        $this->storages = ArrayHelper::merge(
            [
                'file' => ['class' => FileStorage::className()],
            ],
            $this->storages
        );

        $this->_storages = [];
        foreach ($this->storages as $name => $config) {
            $this->_storages[$name] = Yii::createObject($config);
        }
    }

    /**
     * Creates a new file model and returns it.
     *
     * @param array $config model configuration.
     * @return File file model instance.
     */
    public function createFile(array $config = [])
    {
        $modelClass = $this->modelClass;
        return new $modelClass($config);
    }

    /**
     * Saves a file resource using the given configuration.
     *
     * @param ResourceInterface $resource file resource instance.
     * @param array $config configuration for the operation.
     * @return File file model instance.
     * @throws Exception if the file cannot be saved.
     */
    public function saveFile(ResourceInterface $resource, array $config = [])
    {
        $modelConfig = ArrayHelper::remove($config, 'file', []);
        $storageConfig = ArrayHelper::remove($config, 'storage', []);
        $name = ArrayHelper::remove($config, 'name', $resource->getName());
        $extension = ArrayHelper::remove($config, 'extension', $resource->getExtension());
        $folder = ArrayHelper::remove($config, 'folder');

        $model = $this->createFile($modelConfig);
        $model->setAttributes([
            'name' => $this->normalizeFilename($name),
            'extension' => $extension,
            'folder' => $folder,
            'type' => $resource->getType(),
            'size' => $resource->getSize(),
            'hash' => $resource->getHash(),
            'storage' => ArrayHelper::remove($storageConfig, 'name', $this->defaultStorage),
        ]);
        if (!$model->save()) {
            throw new Exception('Failed to save file model.');
        }

        $storageConfig['filename'] = $model->getFilePath();
        if (!$this->getStorage($model->storage)->saveFile($model, $resource->getContents())) {
            throw new Exception("Failed to save file to storage '{$model->storage}'.");
        }
        return $model;
    }

    /**
     * Returns a file model instance using the given search condition.
     *
     * @return \yii\db\ActiveQuery active query instance.
     */
    public function findFile()
    {
        /** @var File $modelClass */
        $modelClass = $this->modelClass;
        return $modelClass::find();
    }

    /**
     * Deletes a file both from its storage and the database.
     *
     * @param integer $id file model id.
     * @return bool whether the operation was successful.
     * @throws Exception
     */
    public function deleteFile($id)
    {
        $model = $this->findFile()->where(['id' => $id])->one();
        if (!$model) {
            throw new Exception('Failed to find file model to delete.');
        }
        if (!$this->getStorage($model->storage)->deleteFile($model)) {
            throw new Exception("Failed to delete file from storage '{$model->storage}'.");
        }
        if (!$model->delete()) {
            throw new Exception('Failed to delete file model.');
        }
        return true;
    }

    /**
     * Returns the URL to a specific file.
     *
     * @param integer $id file model id.
     * @return string file URL.
     */
    public function getFileUrl($id)
    {
        $model = $this->findFile()->where(['id' => $id])->one();
        return $this->getStorage($model->storage)->getFileUrl($model);
    }

    /**
     * Returns the full path for a specific model.
     *
     * @param File $model file model.
     * @return string file path.
     */
    public function getFilePath(File $model)
    {
        return $this->getStorage($model->storage)->getFilePath($model);
    }

    /**
     * Normalizes the given filename by removing illegal characters.
     *
     * @param string $name the filename.
     * @return string the normalized filename.
     */
    protected function normalizeFilename($name)
    {
        return strtolower(str_replace('+', '-', preg_replace('/%[A-Z0-9]{2}/', '', urlencode($name))));
    }

    /**
     * Returns a specific storage component.
     *
     * @param string $name storage component name.
     * @return StorageInterface storage instance.
     */
    protected function getStorage($name)
    {
        if (!isset($this->_storages[$name])) {
            throw new InvalidParamException("Trying to get unknown storage '$name'.");
        }
        return $this->_storages[$name];
    }
}
