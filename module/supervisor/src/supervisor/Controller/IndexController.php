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
            if($this->getRequest()->getPost('save-supprofile')){
                $supemail =$this->getRequest()->getPost('sup-email');
                $supmobile =$this->getRequest()->getPost('sup-mobile');
                $suppass =$this->getRequest()->getPost('suppass');
                $id =$this->getRequest()->getPost('supidprofile');
                
                $config = $this->getServiceLocator()->get('Config');
                $staticSalt = $config['static_salt'];
                $md = MD5($suppass);
                $passsault = $staticSalt.$md;

                if ($suppass != ""){
                    $data = array(
                        'e_mail' => $supemail,
                        'phone' => $supmobile,
                        'super_pass' => $suppass,
                        'super_salt' => $passsault,
                    );
                }else{
                    $data = array(
                        'e_mail' => $supemail,
                        'phone' => $supmobile,
                    );
                }
                
                $sql = new Sql($dba);
                $update = $sql->update();
                $update->table('supervisor');
                $update->set($data);
                $update->where(array('supervisor_id' => $id));
                $statement = $sql->prepareStatementForSqlObject($update);
                $statement->execute();
            }
            $username = $container->id;
            $sql ="SELECT * from supervisor Where supervisor_id=$username";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            
            return new ViewModel(array(
                'Supinfo' => $resultSet
            ));
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
            $wid = $this->params()->fromQuery('wid');
            $sql ="SELECT * from students,teacher,supervisor,companies Where supervisor.supervisor_id=$username
                   AND supervisor_id = super_id
                   AND 	supervisor.Company_ID = companies.Company_ID
                   AND  Teacher_id = mentor_id";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $resultSet->buffer();
            
            $sid = $this->params()->fromQuery('sid');
            $wid = $this->params()->fromQuery('wid');
            $step =1;
            
            if ($wid==3 || $wid==6){
                
                $sql2 ="SELECT * from weeklyreviews Where sid=$sid"
                        . " AND wid=$wid";
                $statement2 = $dba->query($sql2, array(5));
                $resultSet2 = new ResultSet;
                $resultSet2->initialize($statement2);
                $resultSet2->buffer();
                $count = $resultSet2->count();
                if ($this->getRequest()->getPost('submit-weekeval')){
                    if ($count==0){
                        $step = 2;
                        $this->insertweekeval($sid, $wid);
                    }else{
                        $step = 3;
                        $this->updateweekeval($sid, $wid);
                    }
                }
                
                $sql2 ="SELECT * from weeklyreviews Where sid=$sid"
                        . " AND wid=$wid";
                $statement2 = $dba->query($sql2, array(5));
                $resultSet2 = new ResultSet;
                $resultSet2->initialize($statement2);
                $resultSet2->buffer();
                $count = $resultSet2->count();
                
                if ($count==0){
                    return new ViewModel(array(
                        'info' => $resultSet,
                        'sid' => $sid,
                        'step' => $step,
                        'count' => $count,
                        'wid' => $wid
                     ));
                }else{
                    return new ViewModel(array(
                        'info' => $resultSet,
                        'review' => $resultSet2,
                        'sid' => $sid,
                        'step' => $step,
                        'count' => $count,
                        'wid' => $wid
                     ));
                }
            }else{
                return new ViewModel(array(
                    'info' => $resultSet,
                    'sid' => $sid,
                    'step' => $step,
                    'count' => 0,
                    'wid' => $wid
                 ));
            }
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function insertweekeval($sid, $wid){
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 3){
            $q1 =$this->getRequest()->getPost('q1');
            $q2 =$this->getRequest()->getPost('q2');
            $q3 =$this->getRequest()->getPost('q3');
            $q4 =$this->getRequest()->getPost('q4');
            $q5 =$this->getRequest()->getPost('q5');
            $q6 =$this->getRequest()->getPost('q6');
            $q7 =$this->getRequest()->getPost('q7');

            $sql = new Sql($dba);
            $insert = $sql->insert('weeklyreviews');
            //mentor_comment	
            $newData = array('sid'=> $sid,
                'Q1' => $q1,
                'Q2' => $q2,
                'Q3' => $q3,
                'Q4' => $q4,
                'Q5' => $q5,
                'Q6' => $q6,
                'Q7' => $q7,
                'wid' => $wid,
            );
            $insert->values($newData);
            $Query = $sql->getSqlStringForSqlObject($insert);
            $statement = $dba->query($Query);
            $statement->execute();  
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }        
    }
    
    public function updateweekeval($sid, $wid){
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 3){
            $data = array(
                'Q1'  => $this->getRequest()->getPost('q1'),     
                'Q2'  => $this->getRequest()->getPost('q2'),     
                'Q3'  => $this->getRequest()->getPost('q3'),     
                'Q4'  => $this->getRequest()->getPost('q4'),     
                'Q5'  => $this->getRequest()->getPost('q5'),     
                'Q6'  => $this->getRequest()->getPost('q6'),     
                'Q7'  => $this->getRequest()->getPost('q7'),    
            );      

            $username = $container->id;

            $sql = new Sql($dba);
            $update = $sql->update();
            $update->table('weeklyreviews');
            $update->set($data);
            $update->where(array('wid' => $wid, 'sid' => $sid));
            $statement = $sql->prepareStatementForSqlObject($update);
            $statement->execute();
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
            
            $sid = $this->params()->fromQuery('sid');
            $step =1;
                            
            $sql2 ="SELECT * from finalreviews Where sid=$sid";
            $statement2 = $dba->query($sql2, array(5));
            $resultSet2 = new ResultSet;
            $resultSet2->initialize($statement2);
            $resultSet2->buffer();
            $count = $resultSet2->count();
            if ($this->getRequest()->getPost('submit-finaleval')){
                if ($count==0){
                    $step = 2;
                    $this->insertfinaleval($sid);
                }else{
                    $step = 3;
                    $this->updatefinaleval($sid);
                }
            }

            $sql2 ="SELECT * from finalreviews Where sid=$sid";
            $statement2 = $dba->query($sql2, array(5));
            $resultSet2 = new ResultSet;
            $resultSet2->initialize($statement2);
            $resultSet2->buffer();
            $count = $resultSet2->count();

            if ($count==0){
                return new ViewModel(array(
                    'info' => $resultSet,
                    'sid' => $sid,
                    'step' => $step,
                    'count' => $count
                 ));
            }else{
                return new ViewModel(array(
                    'info' => $resultSet,
                    'review' => $resultSet2,
                    'sid' => $sid,
                    'step' => $step,
                    'count' => $count
                 ));
            }
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
       }
    }
    
    public function insertfinaleval($sid){
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 3){
            $q1 =$this->getRequest()->getPost('q1');
            $q2 =$this->getRequest()->getPost('q2');
            $q3 =$this->getRequest()->getPost('q3');
            $q4 =$this->getRequest()->getPost('q4');
            $q5 =$this->getRequest()->getPost('q5');
            $q6 =$this->getRequest()->getPost('q6');
            $q7 =$this->getRequest()->getPost('q7');
            $avg =$this->getRequest()->getPost('avg');
            $absent =$this->getRequest()->getPost('absence');
            $late =$this->getRequest()->getPost('late');
            $strengths =$this->getRequest()->getPost('txt_strength');
            $weaknesses =$this->getRequest()->getPost('txt_weakness');
            $comments =$this->getRequest()->getPost('txt_comments');
            $sql = new Sql($dba);
            $insert = $sql->insert('finalreviews');
            //mentor_comment	
            $newData = array('sid'=> $sid,
                'Q1' => $q1,
                'Q2' => $q2,
                'Q3' => $q3,
                'Q4' => $q4,
                'Q5' => $q5,
                'Q6' => $q6,
                'Q7' => $q7,
                'AVG' => $avg,
                'Absent_Days' => $absent,
                'Late_Times' => $late,
                'Strengths' => $strengths,
                'Weaknesses' => $weaknesses,
                'Comments' => $comments,
                );
            $insert->values($newData);
            $Query = $sql->getSqlStringForSqlObject($insert);
            $statement = $dba->query($Query);
            $statement->execute();  
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }        
    }
    
    public function updatefinaleval($sid){
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 3){
            $data = array(
                'Q1'  => $this->getRequest()->getPost('q1'),     
                'Q2'  => $this->getRequest()->getPost('q2'),     
                'Q3'  => $this->getRequest()->getPost('q3'),     
                'Q4'  => $this->getRequest()->getPost('q4'),     
                'Q5'  => $this->getRequest()->getPost('q5'),     
                'Q6'  => $this->getRequest()->getPost('q6'),     
                'Q7'  => $this->getRequest()->getPost('q7'),
                'AVG' =>  $this->getRequest()->getPost('avg'),
                'Absent_Days' =>  $this->getRequest()->getPost('absence'),
                'Late_Times' =>  $this->getRequest()->getPost('late'),
                'Strengths' =>  $this->getRequest()->getPost('txt_strength'),
                'Weaknesses' =>  $this->getRequest()->getPost('txt_weakness'),
                'Comments' =>  $this->getRequest()->getPost('txt_comments'),

            );      

            $username = $container->id;

            $sql = new Sql($dba);
            $update = $sql->update();
            $update->table('finalreviews');
            $update->set($data);
            $update->where(array('sid' => $sid));
            $statement = $sql->prepareStatementForSqlObject($update);
            $statement->execute();
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
     }    
    
    public function supstwrAction(){
        $auth = new AuthenticationService();
        $container = new Container('username');
        if ($auth->hasIdentity() && $container->type == 3){
            $sm =$this->getServiceLocator();
            $dba = $sm->get($container->adapter);
            $username = $container->id;
            $sid = $this->params()->fromQuery('sid');

            $sql ="SELECT * from students,teacher,supervisor,companies Where supervisor.supervisor_id=".$username."
                   AND supervisor_id = super_id
                   AND 	supervisor.Company_ID = companies.Company_ID
                   AND  Teacher_id = mentor_id";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $resultSet->buffer();

            $found=0;
            $tid='';
            $tp='';
            $ns='';
            $cs='';
            $sc='';
            $mc='';
            $superc='';
            $weekid = $this->params()->fromQuery('wid');

            if  ($weekid >=1 && $weekid<=8){  
                $weekid = $weekid;
                $step=2;
            }else{   
                $weekid = 0;
                $step  = 1;
            }

            $sql2 ="SELECT * from task Where sid=$sid
                   AND week_id=$weekid";
            $statement2 = $dba->query($sql2, array(5));
            $resultSet2 = new ResultSet;         
            $resultSet2->initialize($statement2);
            $resultSet2->buffer();
            $found = $resultSet2->count();
            foreach ($resultSet2 as $task):
                $tid=$task['task_id'];
                $tp= $task['task_performed'];
                $ns= $task['new_skills'];
                $cs= $task['college_skills'];
                $sc= $task['student_comment'];
                $mc= $task['mentor_comment'];
                $superc= $task['supervisor_comment'];
            endforeach;

            if($this->getRequest()->getPost('submit-week')){
                if ($found =="0"){
                      //      echo "found = 00000";echo 'taskid ' .$tid;
                    $this->supinsertweek($weekid, $sid);
                    $step=3;
                }else{
                     //   echo "found = 11111";
                    $this->supupdateweek($tid);
                    $step=4;
                }
            }

            return new ViewModel(array(
                'info' => $resultSet,
                'sid' => $sid,
                'weekid' =>  $weekid,
                'step' =>  $step,
                'tp'=> $tp,
                'ns'=> $ns,
                'cs'=> $cs,
                'sc'=> $sc,
                'mc' => $mc,
                'superc' => $superc,
                'found'=> $found
             ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function supupdateweek ($taskid)  
    {
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 3){
            $data = array(
                'supervisor_comment'  => $this->getRequest()->getPost('txt_super_comment')               
            );
            $sql = new Sql($dba);
            $update = $sql->update();
            $update->table('task');
            $update->set($data);
            $update->where(array('task_id' => $taskid));
            $statement = $sql->prepareStatementForSqlObject($update);
            $statement->execute();
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function supinsertweek($wid, $sid)
            {
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 3){
            $txt_super_comment =$this->getRequest()->getPost('txt_super_comment');

            $sql = new Sql($dba);
            $insert = $sql->insert('task');
            //mentor_comment	
            $newData = array('sid'=> $sid,
                'week_id' => $wid,
                'supervisor_comment' => $txt_super_comment,
            );
            $insert->values($newData);
            $Query = $sql->getSqlStringForSqlObject($insert);
            $statement = $dba->query($Query);
            $statement->execute();  
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
        
              
    }

     /* public function finalreportAction(){
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
            
            $sql2 ="SELECT * from weeklyreviews  Where sid=".$sid;
            $statement2 = $dba->query($sql2, array(5));
            $resultSet2 = new ResultSet;
            $resultSet2->initialize($statement2);
            $resultSet2->buffer();
            $count = $resultSet2->count();
            $step = 0;
              if($this->getRequest()->getPost('submit-weekreport')){
                if ($count == 0){
                   $this->insertweeklyreviews($sid);
                   
                    $step=1;
                }else{
                    $this->updateweeklyreviews($sid);
                    $step=2;
                }
            }
            return new ViewModel(array(
                'info' => $resultSet,
                'sid' => $sid,
                'step' => $step,
                'res2' => $resultSet2,
                'count' => $count
             ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
         public function updateweeklyreviews($sid){
             echo 'update';
        
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
      
         public function insertweeklyreviews($sid){
            echo 'insert';
            
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
          
    } */ 
}
