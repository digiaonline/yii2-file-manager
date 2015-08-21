<?php

namespace nord\yii\filemanager\resources;

use finfo;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class FileResource extends Component implements ResourceInterface
{
    /**
     * @var string
     */
    public $uri;

    /** @var string */
    private $_contents;
    /** @var finfo  */
    private $_finfo;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->uri) {
            throw new InvalidConfigException('FileResource::$uri must be set.');
        }
        $this->_contents = file_get_contents($this->uri);
        $this->_finfo = new finfo(FILEINFO_MIME_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return substr(basename($this->uri), 0, strrpos(basename($this->uri), '.'));
    }

    /**
     * @inheritdoc
     */
    public function getExtension()
    {
        return substr($this->uri, strrpos($this->uri, '.') + 1);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->_finfo->buffer($this->_contents);
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        return strlen($this->_contents);
    }

    /**
     * @inheritdoc
     */
    public function getHash()
    {
        return md5($this->_contents);
    }


    /**
     * @inheritDoc
     */
    public function getContents()
    {
        return $this->_contents;
    }

}
