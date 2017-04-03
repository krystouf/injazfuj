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
        $auth = new AuthenticationService();
        $container = new Container('username');
        if ($auth->hasIdentity() && $container->type == 3){
            $sm =$this->getServiceLocator();
            $dba = $sm->get($container->adapter);
            $username = $container->id;
            $sid = $this->params()->fromQuery('sid');
            $sql ="SELECT * from students,teacher,supervisor,companies Where supervisor.supervisor_id=$username
                   AND supervisor_id = super_id
                   AND 	supervisor.Company_ID = companies.Company_ID
                   AND  Teacher_id = mentor_id";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $resultSet->buffer();
            
            $sql2 ="SELECT * from workplan  Where sid=".$sid;
            $statement2 = $dba->query($sql2, array(5));
            $resultSet2 = new ResultSet;
            $resultSet2->initialize($statement2);
            $resultSet2->buffer();
            $count = $resultSet2->count();
            $step = 1;
            if($this->getRequest()->getPost('wkplan-but')){
                if ($count == 0){
                    $this->insertwkplan($sid);
                    $step=2;
                }else{
                    $this->updatewkplan($sid);
                    $step=3;
                }
            }
            
            $sql2 ="SELECT * from workplan  Where sid=".$sid;
            $statement2 = $dba->query($sql2, array(5));
            $resultSet2 = new ResultSet;
            $resultSet2->initialize($statement2);
            $resultSet2->buffer();
            
            return new ViewModel(array(
                'info' => $resultSet,
                'sid' => $sid,
                'step' => $step,
                'stwkplan' => $resultSet2
             ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function insertwkplan($sid){
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 3){
            $i =0;
            while ($i<8){
                $i++;
                $dep =$this->getRequest()->getPost('dep'.$i);
                $wkassign =$this->getRequest()->getPost('wkassign'.$i);   
                $sdep =$this->getRequest()->getPost('sdep'.$i);  

                $username = $container->id;

                $sql = new Sql($dba);
                $insert = $sql->insert('workplan');
                //mentor_comment	
                $newData = array('week'=> $i,
                    'department' => $dep,
                    's_department'=> $sdep,
                    'work_assigned'=>  $wkassign,  
                    'supervisor_id'=> $username,
                    'sid' => $sid,
                );
                $insert->values($newData);
                $Query = $sql->getSqlStringForSqlObject($insert);
                $statement = $dba->query($Query);
                $statement->execute();  
            }
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }        
    }
    
     public function updatewkplan($sid){
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 3){
            $i =0;
            while ($i<8){
                $i++;
                $data = array(
                    'department'  => $this->getRequest()->getPost('dep'.$i),
                    's_department'  => $this->getRequest()->getPost('sdep'.$i),
                    'work_assigned'  => $this->getRequest()->getPost('wkassign'.$i)           
                );      
                
                $username = $container->id;
                
                $sql = new Sql($dba);
                $update = $sql->update();
                $update->table('workplan');
                $update->set($data);
                $update->where(array('week' => $i, 'sid' => $sid, 'supervisor_id' => $username));
                $statement = $sql->prepareStatementForSqlObject($update);
                $statement->execute();
            }
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
     }
       
    public function workplacementAction(){
        $auth = new AuthenticationService();
        $container = new Container('username');
        if ($auth->hasIdentity() && $container->type == 3){
            $sm =$this->getServiceLocator();
            $dba = $sm->get($container->adapter);
            $username = $container->id;
            $sql ="SELECT * from students,teacher,supervisor,companies Where supervisor.supervisor_id=$username
                   AND supervisor_id = super_id
                   AND 	supervisor.Company_ID = companies.Company_ID
                   AND  Teacher_id = mentor_id";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $resultSet->buffer();
            
            return new ViewModel(array(
                'info' => $resultSet
             ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
     
    public function weeklyreportAction(){
        $auth = new AuthenticationService();
        $container = new Container('username');
        if ($auth->hasIdentity() && $container->type == 3){
            $sm =$this->getServiceLocator();
            $dba = $sm->get($container->adapter);
            $username = $container->id;
            $sid = $this->params()->fromQuery('sid');
            $sql ="SELECT * from students,teacher,supervisor,companies Where supervisor.supervisor_id=$username
                   AND supervisor_id = super_id
                   AND 	supervisor.Company_ID = companies.Company_ID
                   AND  Teacher_id = mentor_id";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $resultSet->buffer();
            
            return new ViewModel(array(
                'info' => $resultSet,
                'sid' => $sid
             ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function finalreportAction(){
        $auth = new AuthenticationService();
        $container = new Container('username');
        if ($auth->hasIdentity() && $container->type == 3){
            $sm =$this->getServiceLocator();
            $dba = $sm->get($container->adapter);
            $username = $container->id;
            $sid = $this->params()->fromQuery('sid');
            $sql ="SELECT * from students,teacher,supervisor,companies Where supervisor.supervisor_id=$username
                   AND supervisor_id = super_id
                   AND 	supervisor.Company_ID = companies.Company_ID
                   AND  Teacher_id = mentor_id";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $resultSet->buffer();
            
            return new ViewModel(array(
                'info' => $resultSet,
                'sid' => $sid
             ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
}