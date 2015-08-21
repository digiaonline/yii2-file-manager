<?php

namespace nord\yii\filemanager\storages;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v2\AwsS3Adapter;
use nord\yii\filemanager\models\File;

/**
 * Class S3Storage
 * @package nord\yii\filemanager\storages
 */
class S3Storage extends FlysystemStorage implements StorageInterface
{

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $secret;

    /**
     * @var string
     */
    public $region;

    /**
     * @var string
     */
    public $bucket;

    /**
     * @var string
     */
    public $prefix;


    /**
     * @inheritDoc
     */
    protected function getAdapter()
    {
        $client = S3Client::factory([
            'key'    => $this->key,
            'secret' => $this->secret,
            'region' => $this->region,
        ]);

        return new AwsS3Adapter($client, $this->bucket, $this->prefix);
    }


    /**
     * @inheritDoc
     */
    public function getFileUrl(File $file)
    {
        $key = $this->prefix . '/' . $file->getFilePath();

        return $this->getAdapter()->getClient()->getObjectUrl($this->bucket, $key);
    }

}
