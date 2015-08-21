<?php

namespace nord\yii\filemanager\resources;

interface ResourceInterface
{
    /**
     * Returns the name for the file associated to this resource.
     *
     * @return string file name.
     */
    public function getName();

    /**
     * Returns the extension for the file associated to this resource.
     *
     * @return string file extension.
     */
    public function getExtension();

    /**
     * Returns the mime-type for the file associated to this resource.
     *
     * @return string mime-type.
     */
    public function getType();

    /**
     * Returns the size for the file associated to this resource.
     *
     * @return integer file size.
     */
    public function getSize();

    /**
     * Returns the hash for the file associated to this resource.
     *
     * @return string
     */
    public function getHash();


    /**
     * Returns the contents of this resource
     *
     * @return string
     */
    public function getContents();

}
