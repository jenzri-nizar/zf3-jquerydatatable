<?php
/**
 * User: Jenzri
 * Date: 21/08/2016
 * Time: 15:09
 */

namespace Datatable;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;


class Module
{
   /* public function loadConfiguration(MvcEvent $e)
    {
        $application   = $e->getApplication();
        $sm            = $application->getServiceManager();
        $sharedManager = $application->getEventManager()->getSharedManager();
        $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController','dispatch',
                function($e) use ($sm) {
                    $sm->get('ControllerPluginManager')->get('DataTable')
                        ->getAdapter($sm->get('Zend\Db\Adapter\Adapter'));exit;
                },2
        );
        
    }*/
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $sharedManager = $e->getApplication()->getEventManager()->getSharedManager();
        $sm            = $e->getApplication()->getServiceManager();
        $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController','dispatch',
            function($e) use ($sm) {
                $sm->get('ControllerPluginManager')->get('DataTable')->setAdapter($sm->get('Zend\Db\Adapter\Adapter'));
            },2
        );


        $sm->get('ViewHelperManager')->setFactory('datatable', function($e) use ($sm) {
            $viewHelper = new \Datatable\View\Helper\Datatable(
                $sm
            );

            return $viewHelper;
        });
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' ,
                ),
            ),
        );
    }


    public function getControllerPluginConfig() {
        return array(
            'invokables' => array(
                'DataTable' =>Controller\Plugin\DataTable::class,
            )
        );
    }
}
