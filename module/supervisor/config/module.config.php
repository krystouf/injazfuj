<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace supervisor;


            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            
return array(
    'router' => array(
        'routes' => array(
            //show students 
             'students' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/supervisorshowstudents',
                    'defaults' => array(
                        '__NAMESPACE__' => 'supervisor\Controller',
                        'controller'    => 'Index',
                        'action'        => 'students',
                    ),
                ),
            ),
            //end  of show students   
            //
            
            'workplacement' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/workplacement',
                    'defaults' => array(
                        '__NAMESPACE__' => 'supervisor\Controller',
                        'controller'    => 'Index',
                        'action'        => 'workplacement',
                    ),
                ),
            ),
            //
            //work plan 
             'workplan' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/supervisorworkplan',
                    'defaults' => array(
                        '__NAMESPACE__' => 'supervisor\Controller',
                        'controller'    => 'Index',
                        'action'        => 'workplan',
                    ),
                ),
            ),
            //end  of work plan     
           // Weekly Report
            'weeklyreport' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/Weeklyreport',
                    'defaults' => array(
                        '__NAMESPACE__' => 'supervisor\Controller',
                        'controller'    => 'Index',
                        'action'        => 'Weeklyreport',
                    ),
                ),
            ),
            
           // end of Weekly Report
           'supervisor' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/supervisor',
                    'defaults' => array(
                        '__NAMESPACE__' => 'supervisor\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),

                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'supervisor\Controller\Index' => Controller\IndexController::class
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'supervisor/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'supervisor/index/login' => __DIR__ . '/../view/supervisor/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
