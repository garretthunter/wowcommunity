<?php
/**
 * Created by PhpStorm.
 * User: garrett
 * Date: 12/23/15
 * Time: 8:50 PM
 */

namespace WowCommunity\Warcraft;


class RegionEntity
{

    /**
     * @var array
     */
    protected static $regions = array(
        'China'     => 'cn',
        'Europe'    => 'eu',
        'Korea'     => 'kr',
        'Taiwan'    => 'tw',
        'US'        => 'us',
    );

    public static function getRegions (){
        return static::$regions;
    }
}