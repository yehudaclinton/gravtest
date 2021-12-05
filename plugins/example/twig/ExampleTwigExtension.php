<?php
namespace Grav\Plugin;
use Grav\Common\Twig\Extension\GravExtension;

class ExampleTwigExtension extends GravExtension
{
    public function getName()
    {
        return 'ExampleTwigExtension';
    }
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('example', [$this, 'exampleFunction'])
        ];
    }
    public function exampleFunction()
    {
        return 'something';
    }
}
