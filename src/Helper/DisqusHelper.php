<?php

/*
* This file is part of the KnpDisqusBundle package.
*
* (c) KnpLabs <hello@knplabs.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Knp\Bundle\DisqusBundle\Helper;

use Knp\Bundle\DisqusBundle\Disqus;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

class DisqusHelper implements RuntimeExtensionInterface
{
    private $twig;
    private $disqus;
    private $debug;

    public function __construct(Environment $twig, Disqus $disqus, bool $debug)
    {
        $this->twig = $twig;
        $this->disqus = $disqus;
        $this->debug = $debug;
    }

    public function render($name, array $parameters = array(), $template = '@KnpDisqus/list.html.twig')
    {
        try {
            $content = $this->disqus->fetch($name, $parameters);
        } catch (\Exception $e) {
            if ($this->debug) {
                $error = $e->getMessage();
            } else {
                $error = 'Oops! Seems there are problem with access to disqus.com. Please refresh the page in a few minutes.';
            }
        }

        $sso = $this->disqus->getSsoParameters($parameters);

        $parameters['error'] = $error ?? null;
        $parameters['content'] = $content ?? [];
        $parameters = $parameters + $this->disqus->getParameters();
        $parameters['sso'] = $sso;

        return $this->twig->render($template, $parameters);
    }
}
