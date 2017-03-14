<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace admin;

return array(
    'router' => array(
        'routes' => array(
            'adminsupervisors' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin_supervisors',
                    'defaults' => array(
                        '__NAMESPACE__' => 'admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'supervisors',
                    ),
                ),
            ),
            'adminwkplaces' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin_workplaces',
                    'defaults' => array(
                        '__NAMESPACE__' => 'admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'workplaces',
                    ),
                ),
            ),
            'adminwkp' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin_work',
                    'defaults' => array(
                        '__NAMESPACE__' => 'admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'workplacement',
                    ),
                ),
            ),
            'aminAtt' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin_attenance',
                    'defaults' => array(
                        '__NAMESPACE__' => 'admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'attendance',
                    ),
                ),
            ),
            'streport' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/streport',
                    'defaults' => array(
                        '__NAMESPACE__' => 'admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'streport',
                    ),
                ),
            ),
            'adminaddattendance' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/add_attendance',
                    'defaults' => array(
                        '__NAMESPACE__' => 'admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'addattendance',
                    ),
                ),
            ),
            'adminstudents' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin_students',
                    'defaults' => array(
                        '__NAMESPACE__' => 'admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'students',
                    ),
                ),
            ),
            'deletestudents' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/delete_students',
                    'defaults' => array(
                        '__NAMESPACE__' => 'admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'delete',
                    ),
                ),
            ),
            'adminHome' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        '__NAMESPACE__' => 'admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'admin' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        '__NAMESPACE__' => 'admin\Controller',
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
            'admin\Controller\Index' => Controller\IndexController::class
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'admin/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'admin/index/login' => __DIR__ . '/../view/admin/index/index.phtml',
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
