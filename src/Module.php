<?php
/**
 * User: Jenzri
 * Date: 21/08/2016
 * Time: 15:09
 */

namespace Zf3\Jquerydatatable;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;

class Module implements ConfigProviderInterface, DependencyIndicatorInterface
{

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
            $viewHelper = new \Zf3\Jquerydatatable\View\Helper\Datatable(
                $sm
            );

            return $viewHelper;
        });
    }
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /*public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' ,
                ),
            ),
        );
    }*/


    public function getControllerPluginConfig() {
        return array(
            'invokables' => array(
                'DataTable' =>Controller\Plugin\DataTable::class,
            )
        );
    }

    /**
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array
     */
    public function getModuleDependencies()
    {
        return ['Zend\Paginator','Zend\Db'];
    }
}
