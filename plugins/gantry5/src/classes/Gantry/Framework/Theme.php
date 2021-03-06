<?php

/**
 * @package   Gantry5
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2021 RocketTheme, LLC
 * @license   MIT
 *
 * http://opensource.org/licenses/MIT
 */

namespace Gantry\Framework;

use Gantry\Component\Config\Config;
use Gantry\Component\Content\Block\ContentBlock;
use Gantry\Component\Theme\AbstractTheme;
use Gantry\Component\Theme\ThemeTrait;
use Gantry\Debugger;
use Grav\Common\Config\Config as GravConfig;
use Grav\Common\Grav;
use Grav\Common\Page\Interfaces\PageInterface;
use Grav\Common\Page\Pages;
use Grav\Common\Twig\Twig;
use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extension\EscaperExtension;

/**
 * Class Theme
 * @package Gantry\Framework
 */
class Theme extends AbstractTheme
{
    use ThemeTrait;

    /**
     * Return renderer.
     *
     * @return Environment
     */
    public function renderer()
    {
        if (!$this->renderer) {
            $gantry = static::gantry();
            $grav = Grav::instance();

            /** @var Twig $gravTwig */
            $gravTwig = $grav['twig'];

            $twig = $gravTwig->twig();
            $loader = $gravTwig->loader();

            /** @var Config $global */
            $global = $gantry['global'];

            $debug = $gantry->debug();
            $production = (bool) $global->get('production', 1);

            if ($debug && !$twig->isDebug()) {
                $twig->enableDebug();
                $twig->addExtension(new DebugExtension());
            }

            if ($production) {
                $twig->disableAutoReload();
            } else {
                $twig->enableAutoReload();
            }

            // Force html escaping strategy.
            $twig->getExtension(EscaperExtension::class)->setDefaultStrategy('html');

            $this->setTwigLoaderPaths($loader);

            $this->renderer = $this->extendTwig($twig, $loader);
        }

        return $this->renderer;
    }

    /**
     * @param string|array|object $particle
     * @param array $attribs
     * @return ContentBlock
     */
    public function getParticle($particle, array $attribs = [])
    {
        if (is_string($particle)) {
            $id = $particle;
            $particle = (object)['id' => $particle];
        } else {
            $particle = (object)$particle;
            $id = isset($particle->id) ? $particle->id : null;
        }
        if ($id) {
            // Render module.
            if (preg_match('`^(.*?)-module-(.*)$`', $id, $matches)) {
                list(, $position, $id) = $matches;

                $gantry = Gantry::instance();

                /** @var Platform $platform */
                $platform = $gantry['platform'];

                if (\GANTRY_DEBUGGER) {
                    Debugger::addMessage("Rendering module {$id} in position {$position}", 'debug');
                }

                /** @var Document $document */
                $document = $gantry['document'];
                $document::push();
                $html = trim($platform->displayModule("{$position}/{$id}", $attribs + ['position' => ['key' => $position]]));

                return $document::pop()->setContent($html);
            }

            if (\GANTRY_DEBUGGER) {
                Debugger::addMessage("Rendering particle {$id}", 'debug');
            }

            // Render particle.
            $layout = $this->loadLayout();
            $particle = $layout->find($id);
        }

        if (empty($particle->type) || $particle->type !== 'particle') {
            throw new \RuntimeException('Not Found', 404);
        }

        $context = $attribs + [
            'gantry' => $this,
            'inContent' => false
        ];

        return $this->getContent($particle, $context);
    }

    /**
     * Get list of twig paths.
     *
     * @return array
     */
    public static function getTwigPaths()
    {
        /** @var UniformResourceLocator $locator */
        $locator = static::gantry()['locator'];

        return $locator->mergeResources(['gantry-theme://templates', 'gantry-engine://templates']);
    }

    /**
     * @see AbstractTheme::getContext()
     *
     * @param array $context
     * @return array
     */
    public function getContext(array $context)
    {
        $gantry = static::gantry();
        $grav = Grav::instance();

        /** @var PageInterface $page */
        $page = $grav['page'];

        $context = parent::getContext($context);
        $context = array_replace($context, $grav['twig']->twig_vars);
        $context['site'] = $gantry['site'];

        // Emulate site context.
        if (!isset($context['theme'])) {
            /** @var GravConfig $config */
            $config = $grav['config'];

            $context['theme'] = $config->get('theme');
        }
        if (!isset($context['pages'])) {
            /** @var Pages $pages */
            $pages = $grav['pages'];

            $context['pages'] = $pages->root();
        }
        if (!isset($context['page'])) {
            $context['page'] = $page;
        }
        if (!isset($context['header'])) {
            $context['header'] = $page->header();
        }
        if (!isset($context['media'])) {
            $context['media'] = $page->media();
        }
        if (!isset($context['content'])) {
            $context['content'] = $page->content();
        }

        return $context;
    }
}
