<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;
use Symfony\Component\Yaml\Yaml;


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

//$data = $action->toArray();
//$data = $form->data->toArray();
print_r($form);
//$name = $form->data['name'];
//$this->grav['log']->info('huda test7: '.$name); //$form["data"]


//$this->grav['log']->info('huda test6 '.getcwd());

//$value = Yaml::parseFile('user/accounts/testuser.yaml');
//$this->grav['log']->info('huda test6 '.$value["fullname"]);



        switch ($action) {
            case 'tenantComplaintForm':
              //get current user and add to email
              //get address unit associated to user
        }
    }
}
