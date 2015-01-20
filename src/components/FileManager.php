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

    private $_modelClass;
    private $_storages;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!isset($this->_modelClass)) {
            $this->_modelClass = File::className();
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
        $modelClass = $this->getModelClass();
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
        $storageConfig = ArrayHelper::remove($config, 'storage', []);
        $modelConfig = ArrayHelper::remove($config, 'model', []);

        $name = isset($config['name']) ? $config['name'] : $resource->getName();
        $extension = isset($config['extension']) ? $config['extension'] : $resource->getExtension();
        $model = $this->createFile($modelConfig);
        $model->setAttributes([
            'extension' => $extension,
            'name' => $name,
            'type' => $resource->getType(),
            'size' => $resource->getSize(),
            'hash' => $resource->getHash(),
            'storage' => ArrayHelper::remove($storageConfig, 'name', self::DEFAULT_STORAGE),
        ]);
        if (!$model->save()) {
            throw new Exception('Failed to save file model.');
        }

        $storageConfig['filename'] = $this->getFileName($model);
        $this->getStorage($model->storage)->saveFile($resource, $storageConfig);
        return $model;
    }

    /**
     * Returns a file model instance using the given search condition.
     *
     * @param mixed $condition search condition.
     * @return File file model instance.
     */
    public function findFile($condition)
    {
        /** @var File $modelClass */
        $modelClass = $this->getModelClass();
        return $modelClass::findOne($condition);
    }

    /**
     * Deletes a file both from its storage and the database.
     *
     * @param integer $id file model id.
     * @return boolean whether the operation was successful.
     */
    public function deleteFile($id)
    {
        $model = $this->findFile($id);
        if (!$model) {
            throw new Exception('Failed to find file model to delete.');
        }
        $filename = $this->getFilename($model);
        if (!$this->getStorage($model->storage)->deleteFile($filename)) {
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
        $model = $this->findFile($id);
        $filename = $this->getFileName($model);
        return $this->getStorage($model->storage)->getFileUrl($filename);
    }

    /**
     * Returns the class name for the file model class.
     *
     * @return string class name.
     */
    public function getModelClass()
    {
        return $this->_modelClass;
    }

    /**
     * Sets the class name for the file model class.
     *
     * @param string $modelClass class name.
     */
    public function setModelClass($modelClass)
    {
        $this->_modelClass = $modelClass;
    }

    /**
     * Returns the filename for a specific model.
     *
     * @param File $model file model.
     * @return string filename.
     */
    protected function getFileName(File $model)
    {
        return "{$model->name}-{$model->id}.{$model->extension}";
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