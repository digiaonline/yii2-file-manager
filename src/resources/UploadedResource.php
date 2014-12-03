<?php

namespace nord\yii\filemanager\resources;

use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class UploadedResource extends Component implements ResourceInterface
{
    /** @var UploadedFile */
    private $_file;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->_file) {
            throw new InvalidConfigException('UploadedResource::$uploadedFile must be set.');
        }
        if ($this->_file->hasError) {
            throw new Exception('Failed to upload file with error: ' . $this->getUploadError($this->_file->error));
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_file->name;
    }

    /**
     * @inheritdoc
     */
    public function getExtension()
    {
        return $this->_file->extension;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return FileHelper::getMimeType($this->_file->tempName);
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        return $this->_file->size;
    }

    /**
     * @inheritdoc
     */
    public function getHash()
    {
        return hash_file('md5', $this->_file->tempName);
    }

    /**
     * @inheritdoc
     */
    public function saveAs($path, array $config = [])
    {
        return $this->_file->saveAs($path, isset($config['deleteTempFile']) ? $config['deleteTempFile'] : true);
    }

    /**
     * Sets the file associated to this resource.
     *
     * @param UploadedFile $_file uploaded file instance.
     */
    public function setFile(UploadedFile $_file)
    {
        $this->_file = $_file;
    }

    /**
     * Returns the readable upload error for the given error code.
     *
     * @param int $code error code.
     * @return string error message.
     */
    protected function getUploadError($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File too large.';
            case UPLOAD_ERR_PARTIAL:
                return 'File upload was not completed.';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Temporary folder missing.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload.';
            case UPLOAD_ERR_OK:
            default:
                return 'OK';
        }
    }
}