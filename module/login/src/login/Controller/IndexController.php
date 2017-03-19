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
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

use Zend\Db\Adapter\Adapter as DbAdapter;

use Zend\Authentication\Adapter\DbTable as AuthAdapter;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function loginAction()
    {
        if($this->getRequest()->getPost('student_log_but')){
            $username = $this->getRequest()->getPost('u');
            $pass = $this->getRequest()->getPost('p');
            $this->fauth($username, $pass, 'students');
        }else if($this->getRequest()->getPost('teacher_log_but')){
            $username = $this->getRequest()->getPost('ut');
            $pass = $this->getRequest()->getPost('pt');
            $this->fauth($username, $pass, 'teacher');
        }else if($this->getRequest()->getPost('super_log_but')){
            $username = $this->getRequest()->getPost('us');
            $pass = $this->getRequest()->getPost('ps');
            $this->fauth($username, $pass, 'supervisor');
        }else if($this->getRequest()->getPost('admin_log_but')){
            $username = $this->getRequest()->getPost('ua');
            $pass = $this->getRequest()->getPost('pa');
            $this->fauth($username, $pass, 'admin');
        }else{
            $container = new Container('username');
            if ($this->params()->fromQuery('loc') == "dxb"){
                $container->adapter = "adapter2";
            }else if ($this->params()->fromQuery('loc') == "fuj"){
                $container->adapter = "adapter";
            }else{
                return $this->redirect()->toRoute('index',
                    array('controller'=>'index',
                          'action' => 'index'));
            }
            return new ViewModel(array(
                'location' => $this->params()->fromQuery('loc'),
             ));
        }
    }
    
    public function fauth($username, $pass, $table){
        $sm = $this->getServiceLocator();
        $container = new Container('username');
        $dba = $sm->get($container->adapter);
        
        $config = $this->getServiceLocator()->get('Config');
        $staticSalt = $config['static_salt'];
        
        if ($table == "teacher"){
            $sql = "Select Teacher_id from teacher where username='".$username."'";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $tid = 0;
            foreach ($resultSet as $row){
                $tid = $row['Teacher_id'];
            }
            $authAdapter = new AuthAdapter($dba,
                $table, // there is a method setTableName to do the same
                'username', // there is a method setIdentityColumn to do the same
                'Teacher_pass', // there is a method setCredentialColumn to do the same
                "MD5(CONCAT('$staticSalt', Teacher_salt))" // setCredentialTreatment(parametrized string) 'MD5(?)'
            );
        }else if ($table == "supervisor"){
            $sql = "Select supervisor_id from supervisor where super_name='".$username."'";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $tid = 0;
            foreach ($resultSet as $row){
                $tid = $row['supervisor_id'];
            }
            $authAdapter = new AuthAdapter($dba,
                $table, // there is a method setTableName to do the same
                'super_name', // there is a method setIdentityColumn to do the same
                'super_pass', // there is a method setCredentialColumn to do the same
                "MD5(CONCAT('$staticSalt', super_salt))" // setCredentialTreatment(parametrized string) 'MD5(?)'
            );
        }else if ($table == "admin"){
            $authAdapter = new AuthAdapter($dba,
                $table, // there is a method setTableName to do the same
                'Admin_id', // there is a method setIdentityColumn to do the same
                'Admin_pass', // there is a method setCredentialColumn to do the same
                "MD5(CONCAT('$staticSalt', Admin_salt))" // setCredentialTreatment(parametrized string) 'MD5(?)'
            );
        }else if ($table == "students"){
            $sql = "Select sid from students where Student_id='".$username."'";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $sid = 0;
            foreach ($resultSet as $row){
                $sid = $row['sid'];
            }
            $authAdapter = new AuthAdapter($dba,
                $table, // there is a method setTableName to do the same
                'Student_id', // there is a method setIdentityColumn to do the same
                'student_pass', // there is a method setCredentialColumn to do the same
                "MD5(CONCAT('$staticSalt', student_salt))" // setCredentialTreatment(parametrized string) 'MD5(?)'
            );
        }
        
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
                    if ($table == "teacher"){
                        $container->id = $tid;
                        $container->type= 1;
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
                    }else if ($table == "supervisor"){
                        $container->id = $superid;
                        $container->type= 3;
                        $storage = $auth->getStorage();
                        $storage->write($authAdapter->getResultRowObject(
                                null,
                                'super_pass'
                        ));
                        $time = 1209600; // 14 days 1209600/3600 = 336 hours => 336/24 = 14 days
    //						if ($data['rememberme']) $storage->getSession()->getManager()->rememberMe($time); // no way to get the session
                        $sessionManager = new \Zend\Session\SessionManager();
                        $sessionManager->rememberMe($time);
                        return $this->redirect()->toRoute('supervisor',
                        array('controller'=>'index',
                              'action' => 'index'));
                    }else if ($table == "admin"){
                        $container->id = $username;
                        $container->type= 0;
                        $container->sub="";
                        $storage = $auth->getStorage();
                        $storage->write($authAdapter->getResultRowObject(
                                null,
                                'Admin_pass'
                        ));
                        $time = 1209600; // 14 days 1209600/3600 = 336 hours => 336/24 = 14 days
    //						if ($data['rememberme']) $storage->getSession()->getManager()->rememberMe($time); // no way to get the session
                        $sessionManager = new \Zend\Session\SessionManager();
                        $sessionManager->rememberMe($time);
                        return $this->redirect()->toRoute('admin',
                        array('controller'=>'index',
                              'action' => 'index'));
                    }else if ($table == "students"){
                        $container->id = $sid;
                        $container->type= 2;
                        $container->sub="";
                        $storage = $auth->getStorage();
                        $storage->write($authAdapter->getResultRowObject(
                                null,
                                'student_pass'
                        ));
                        $time = 1209600; // 14 days 1209600/3600 = 336 hours => 336/24 = 14 days
    //						if ($data['rememberme']) $storage->getSession()->getManager()->rememberMe($time); // no way to get the session
                        $sessionManager = new \Zend\Session\SessionManager();
                        $sessionManager->rememberMe($time);
                        return $this->redirect()->toRoute('student',
                        array('controller'=>'index',
                              'action' => 'index'));
                    }
                    
            default:
                    // do stuff for other failure
                    break;
        }						
        
    }
    
    public function logoutAction()
    {
        $auth = new AuthenticationService();
        // or prepare in the globa.config.php and get it from there
        // $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');

        if ($auth->hasIdentity()) {
                $identity = $auth->getIdentity();
        }			

        $auth->clearIdentity();
//		$auth->getStorage()->session->getManager()->forgetMe(); // no way to get the sessionmanager from storage
        $sessionManager = new \Zend\Session\SessionManager();
        $sessionManager->forgetMe();

        return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                  'action' => 'login'));		
    }

}
