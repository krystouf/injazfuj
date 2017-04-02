<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace teacher;

return array(
    'router' => array(
        'routes' => array(
            'attendance' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/attendance',
                    'defaults' => array(
                        '__NAMESPACE__' => 'teacher\Controller',
                        'controller'    => 'Index',
                        'action'        => 'attendance',
                    ),
                ),
            ),
           // my new work
            'toverview' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/overview',
                    'defaults' => array(
                        '__NAMESPACE__' => 'teacher\Controller',
                        'controller'    => 'Index',
                        'action'        => 'toverview',
                    ),
                ),
            ),
            'stwr' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/stwr',
                    'defaults' => array(
                        '__NAMESPACE__' => 'teacher\Controller',
                        'controller'    => 'Index',
                        'action'        => 'stwr',
                    ),
                ),
            ),
            
            'stwp' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/stwp',
                    'defaults' => array(
                        '__NAMESPACE__' => 'teacher\Controller',
                        'controller'    => 'Index',
                        'action'        => 'stwp',
                    ),
                ),
            ),
             // end my new work
            'students' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/students',
                    'defaults' => array(
                        '__NAMESPACE__' => 'teacher\Controller',
                        'controller'    => 'Index',
                        'action'        => 'students',
                    ),
                ),
            ),
            'teacherHome' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/teacher',
                    'defaults' => array(
                        '__NAMESPACE__' => 'teacher\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
            ),
            'teacherwp' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/teacherwp',
                    'defaults' => array(
                        '__NAMESPACE__' => 'teacher\Controller',
                        'controller'    => 'Index',
                        'action'        => 'teacherwp',
                    ),
                ),
            ),
            
            
            'teacherLogout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/teacherLogout',
                    'defaults' => array(
                        '__NAMESPACE__' => 'login\Controller',
                        'controller'    => 'Index',
                        'action'        => 'logout',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'teacher' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/teacher',
                    'defaults' => array(
                        '__NAMESPACE__' => 'teacher\Controller',
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
            'teacher\Controller\Index' => Controller\IndexController::class
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'teacher/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'teacher/index/login' => __DIR__ . '/../view/teacher/index/index.phtml',
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
