<?php
require_once 'autoload.php';
/**
 * Created by PhpStorm.
 * User: Norbert
 * Date: 2016. 06. 20.
 * Time: 21:43
 */

class Ajax extends App\RestApi{
    public function getTest(){
        $this->onSuccess([
            'asd' => 'adas'
        ]);
    }
}
new Ajax();