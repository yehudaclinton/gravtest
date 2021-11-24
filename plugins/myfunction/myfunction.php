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

//$this->grav['log']->info('huda test8: '.$action); //form["data"]]


//$this->grav['log']->info('huda test6 '.getcwd());

//$value = Yaml::parseFile('user/accounts/testuser.yaml');
//$this->grav['log']->info('huda test6 '.$value["fullname"]);

//$stuff = getFilesByField('attachments');
//$this->grav['log']->info('huda test0 '.print_r($stuff));

        switch ($action) {
            case 'form1':
$data = $form->data->toArray();
//$this->grav['log']->info('huda test00 '.print_r($data));
            case 'addpage':
//              $this->grav['log']->info('huda test8: '.$name);
$name = $form->data['title'];

$path = str_replace(' ', '-', strtolower($name));
$path = 'user/pages/08.catalog/'.$path.'/item.md';
$content = file_get_contents($path);
//$this->grav['log']->info('huda test8: '.$content);
$arr = preg_split("/\s+/", $content);
$picture = $arr[8];
$newcontnent = str_replace("honeypot: ''\n---", "honeypot: ''\n---\n![alt](".$picture.")\n", $content);
$outcome = file_put_contents($path, $newcontnent);
//$this->grav['log']->info('huda test8: success:'.$outcome);
        }
    }
}
