<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace login\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Session\Container;

use Zend\Db\Adapter\Adapter as DbAdapter;

use Zend\Authentication\Adapter\DbTable as AuthAdapter;

class IndexController extends AbstractActionController
{
    public function loginAction()
    {
        if($this->getRequest()->getPost('student_log_but')){
            return $this->redirect()->toRoute('student',
            array('controller'=>'index',
                  'action' => 'index'));
        }else if($this->getRequest()->getPost('teacher_log_but')){
            $username = $this->getRequest()->getPost('u');
            $pass = $this->getRequest()->getPost('p');
            $this->fauth($username, $pass, 'teacher');
        }else if($this->getRequest()->getPost('admin_log_but')){
            return $this->redirect()->toRoute('admin',
            array('controller'=>'index',
                  'action' => 'index'));
        }else{
            return new ViewModel();
        }
    }
    
    public function fauth($username, $pass, $table){
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $config = $this->getServiceLocator()->get('Config');
        $staticSalt = $config['static_salt'];

        $authAdapter = new AuthAdapter($dbAdapter,
                $table, // there is a method setTableName to do the same
                'Teacher_id', // there is a method setIdentityColumn to do the same
                'Teacher_pass', // there is a method setCredentialColumn to do the same
                "MD5(CONCAT('$staticSalt', Teacher_salt))" // setCredentialTreatment(parametrized string) 'MD5(?)'
           );
        $authAdapter
                ->setIdentity($username)
                ->setCredential($pass)
        ;

        $auth = new AuthenticationService();
        // or prepare in the globa.config.php and get it from there. Better to be in a module, so we can replace in another module.
        // $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        // $sm->setService('Zend\Authentication\AuthenticationService', $auth); // You can set the service here but will be loaded only if this action called.
        $result = $auth->authenticate($authAdapter);			

        switch ($result->getCode()) {
            case Result::FAILURE_IDENTITY_NOT_FOUND:
                    // do stuff for other failure
                    break;

            case Result::FAILURE_CREDENTIAL_INVALID:
                    // do stuff for other failure
                    break;

            case Result::SUCCESS:
                    $container = new Container('user');
                    $container->username = $username;
                    $storage = $auth->getStorage();
                    $storage->write($authAdapter->getResultRowObject(
                            null,
                            'Teacher_pass'
                    ));
                    $time = 1209600; // 14 days 1209600/3600 = 336 hours => 336/24 = 14 days
//						if ($data['rememberme']) $storage->getSession()->getManager()->rememberMe($time); // no way to get the session
                    $sessionManager = new \Zend\Session\SessionManager();
                    $sessionManager->rememberMe($time);
                    return $this->redirect()->toRoute('teacher',
                    array('controller'=>'index',
                          'action' => 'index'));
            default:
                    // do stuff for other failure
                    break;
        }						
        
    }

}
