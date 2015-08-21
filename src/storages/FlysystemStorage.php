<?php

namespace nord\yii\filemanager\storages;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use nord\yii\filemanager\models\File;
use yii\base\Exception;
use yii\base\Component;

abstract class FlysystemStorage extends Component implements StorageInterface
{

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var Filesystem
     */
    protected $filesystem;


    /**
     * @return AdapterInterface
     */
    abstract protected function getAdapter();


    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->filesystem = new Filesystem($this->getAdapter());
    }


    /**
     * @inheritDoc
     */
    public function saveFile(File $file, $contents)
    {
        return $this->filesystem->put($file->getFilePath(), $contents);
    }


    /**
     * @inheritDoc
     */
    public function deleteFile(File $file)
    {
        return $this->filesystem->delete($file->getFilePath());
    }


    /**
     * @inheritDoc
     */
    public function fileExists(File $file)
    {
        return $this->filesystem->has($file->getFilePath());
    }


    /**
     * @inheritDoc
     */
    public function getFilePath(File $file)
    {
        throw new Exception("This storage doesn't support retrieving file paths");
    }

}
