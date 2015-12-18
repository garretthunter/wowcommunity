<?php
/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 12/8/2015
 * Time: 10:16 PM
 */

namespace WowCommunity\Plugin;

use WowCommunity\Core\AbstractEntity;

class OptionsEntity extends AbstractEntity
{

    protected $options = [
        "apiKey"    =>  "",
        "region"    =>  "",
        "realm"     =>  "",
        "guild"     =>  "",
    ];

    function __construct(array $body)
    {
        parent::__construct($body);
    }

    function getAll ()
    {
        return get_object_vars($this);
    }


}