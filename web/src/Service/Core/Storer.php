<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 1/27/2021
 * Time: 3:32 PM
 */

namespace App\Service\Core;


use Tchoulom\ViewCounterBundle\Adapter\Storage\StorageAdapterInterface;

class Storer implements StorageAdapterInterface
{

    /**
     * Saves the statistics.
     *
     * @param $stats
     */
    public function save($stats)
    {
        // TODO: Implement save() method.
    }

    /**
     * Loads the contents.
     *
     * @return mixed
     */
    public function loadContents()
    {
        // TODO: Implement loadContents() method.
    }
}