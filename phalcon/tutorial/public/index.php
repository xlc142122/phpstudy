<?php
/**
 * Created by PhpStorm.
 * User: Pasenger
 * Date: 2015/1/29
 * Time: 20:10
 */

try {
    //Register an autoloader
    $loader = new \Phalcon\Loader();
    $loader->registerDirs(array(
        '../app/controllers/',
        '../app/models/'
    ))->register();

    //Create a DI
    $di = new Phalcon\DI\FactoryDefault();

    //Setup the view component
    $di->set('view', function(){
        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir('../app/views/');
        return $view;
    });

    //Setup a base URI so that all generated URIs include the 'tutorial' folder
//    $di->set('url', function(){
//        $url = new \Phalcom\Mvc\Url();
//        $url->setBaseUri('/tutorial/');
//
//        return $url;
//    });

    //Handle the request
    $application = new \Phalcon\Mvc\Application();
    $application->setDI($di);

    echo $application->handle()->getContent();
}catch (\Phalcon\Exception $e){
    echo "PhalconException: ", $e->getMessage();
}