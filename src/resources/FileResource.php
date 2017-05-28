<?php

namespace nord\yii\filemanager\resources;

use finfo;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

class FileResource extends Component implements ResourceInterface
{
    /**
     * @var string
     */
    public $uri;

    /**
     * @var resource|null the stream context to use when retrieving files
     */
    public $streamContext;

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
        
        // Use the specified stream context when available
        $this->_contents = file_get_contents($this->uri, false, $this->streamContext);
        $this->_finfo = new finfo(FILEINFO_MIME_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        if (strpos($this->uri, '.') !== false) {
            return substr(basename($this->uri), 0, strrpos(basename($this->uri), '.'));
        } else {
            return basename($this->uri);
        }
    }

    /**
     * @inheritdoc
     */
    public function getExtension()
    {
        // Generate the extension based on the MIME type if possible, eliminates inconsistencies
        $extensions = FileHelper::getExtensionsByMimeType($this->getType());
        $mimeType   = end($extensions);

        // Fallback on the file extension
        if ($mimeType === false) {
            $mimeType = substr($this->uri, strrpos($this->uri, '.') + 1);
        }

        return $mimeType;
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
