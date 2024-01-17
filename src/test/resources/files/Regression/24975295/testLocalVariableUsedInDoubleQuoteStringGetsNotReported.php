<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

use Route24975295 as Route;

class Bootstrap extends Bootstrap24975295
{
    protected function initRouter()
    {
        $this->bootstrap('frontController');
        $front = $this->getResource('frontController');
        $router = $front->getRouter();

        $route = new Hostname24975295('foo', array('module' => 'default'));
        $router
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))))
            ->addRoute('default', $route->chain(new Route('x', array('controller' => 'index'))));
    }
}
