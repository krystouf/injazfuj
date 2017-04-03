<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace supervisor\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Sql\Sql;
use Zend\View\Model\ViewModel;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;

class IndexController extends AbstractActionController
{    

    public function indexAction(){
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 3){
            return new ViewModel(); 
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
 
    public function workplanAction(){
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        $username = $container->id;
        $sql ="SELECT * from students,teacher,supervisor Where super_id=".$username."
              AND sid = sid
               AND  Teacher_id = mentor_id";
        $statement = $dba->query($sql, array(5));
        $resultSet = new ResultSet;
        $resultSet->initialize($statement);
        
        $sql2 ="SELECT * from workplan  Where super_id=".$username;
        $statement2 = $dba->query($sql2, array(5));
        $resultSet2 = new ResultSet;
        $resultSet2->initialize($statement2);
        
        return new ViewModel(array(
            'Studentinfo' => $resultSet,
             'workplans' => $resultSet2,
         ));
    }
       
    //  end of workplan Action 
        public function workplacementAction(){
       /*
            $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        $username = $container->id;
        $sql ="SELECT * from students,teacher,supervisor Where super_id=".$username."
              AND sid = sid
               AND  Teacher_id = mentor_id";
        $statement = $dba->query($sql, array(5));
        $resultSet = new ResultSet;
        $resultSet->initialize($statement);
        
        $sql2 ="SELECT * from workplan  Where super_id=".$username;
        $statement2 = $dba->query($sql2, array(5));
        $resultSet2 = new ResultSet;
        $resultSet2->initialize($statement2);
        
        return new ViewModel(array(
            'Studentinfo' => $resultSet,
             'workplans' => $resultSet2,
         ));
        * 
        */
            return new ViewModel();
    }
     
}