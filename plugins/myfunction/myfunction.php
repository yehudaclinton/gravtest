<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

class myfunction extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'onFormProcessed' => ['onFormProcessed', 0]
        ];
    }


    public function onFormProcessed(Event $event)
    {
        $form = $event['form'];
        $action = $event['action'];
        $params = $event['params'];

$data = json_decode( json_encode($form["data"]), true);
foreach($data as $val){
    if(!is_array($val)){
      $x=0;
      $myvar[$x]=$val;
    }
}

$this->grav['log']->info('huda test5: '.$myvar[0]); //$form["data"]
//$this->grav['log']->info('huda test4: '.var_dump(json_decode($form["data"])));

        switch ($action) {
            case 'yourAction':
                //do what you want
//$Grav['log']->info('huda test');
        }
    }
}
