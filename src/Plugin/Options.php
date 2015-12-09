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

    protected $apikey;
    protected $region = 'us';
    protected $realm;
    protected $guild;

    function __construct(array $body)
    {
        parent::__construct($body);
    }

    function getAll ()
    {
        return get_object_vars($this);
    }


}