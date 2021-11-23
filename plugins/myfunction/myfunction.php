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
//print_r($form);

$name = $form->data['title'];
//$this->grav['log']->info('huda test8: '.$action); //$form["data"]


//$this->grav['log']->info('huda test6 '.getcwd());

//$value = Yaml::parseFile('user/accounts/testuser.yaml');
//$this->grav['log']->info('huda test6 '.$value["fullname"]);



        switch ($action) {
            case 'addpage':
//              $this->grav['log']->info('huda test8: '.$name);

$path = str_replace(' ', '-', strtolower($name));
//$pictures = scandir('user/pages/08.catalog/'.$path);
$pictures = $form->data['attachments'];
$path = 'user/pages/08.catalog/'.$path.'/item.md';
$this->grav['log']->info('huda test9: '.$pictures);
$content = file_get_contents($path);
//$this->grav['log']->info('huda test8: '.$content);
$newcontnent = str_replace("honeypot: ''\n---", "honeypot: ''\n---\n![alt](".$pictures.")\n", $content);
$outcome = file_put_contents($path, $newcontnent);
//$this->grav['log']->info('huda test8: success:'.$outcome);
        }
    }
}
