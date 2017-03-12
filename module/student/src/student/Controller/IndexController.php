<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace student\Controller;

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
    public function indexAction()
    {   
        return new ViewModel();
    }
    
    public function workplacementAction(){
       //$auth = new AuthenticationService();
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 2){
            $username = $container->id;
            $sql ="SELECT * from students,teacher,supervisor Where sid=".$username."
                   AND supervisor_id = super_id
                   AND  Teacher_id = mentor_id";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);

            return new ViewModel(array(
                'Studentinfo' => $resultSet,
             ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
     
    public function weeklyreportAction()
    {   
       date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 2){
            //$step=0;
            $found=0;
            $tid='';
            $tp='';
            $ns='';
            $cs='';
            $sc='';
            // echo 'found =' . $found;
            $weekid = $this->params()->fromQuery('wid');

            if  ($weekid >=1 && $weekid<=8){  
                $weekid = $weekid;
                $step=2;
            }else{   
                $weekid = 0;
                $step  = 1;
            }
            
            $username =$container->id;
            $sql ="SELECT * from task  Where sid=".$username."
                   AND week_id =".$weekid."";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;         
            $resultSet->initialize($statement);

            foreach ($resultSet as $task):
                $tid=$task['task_id'];
                $tp= $task['task_performed'];
                $ns= $task['new_skills'];
                $cs= $task['college_skills'];
                $sc= $task['student_comment'];
                //    $step =4;
               //     $weekid=1;
                $found=1;
            endforeach;
                  /*
             if ($found =="0")        
                   {echo "found = 00000";echo 'taskid ' .$tid;}
            else
                  {echo "found = 11111";echo 'taskid ' .$tid;}   
            */
            if($this->getRequest()->getPost('submit-week')){
                if ($found =="0"){
                      //      echo "found = 00000";echo 'taskid ' .$tid;
                    $this->insertweek(11,$weekid);
                    $step=3;
                }else{
                     //   echo "found = 11111";
                    $this->updateweek($tid);
                    $step=4;
                }
            }

            return new ViewModel(array(
                'weekid' =>  $weekid,
                'step' =>  $step,
                'tp'=> $tp,
                'ns'=> $ns,
                'cs'=> $cs,
                'sc'=> $sc,
                'found'=> $found,
            ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
            
            
    } // end of weekly reportAction
    
    
    public function insertweek($stid,$wid)
            {
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 2){
            $task_performed =$this->getRequest()->getPost('txt_task_performed');
            $new_skills =$this->getRequest()->getPost('txt_new_skills');   
            $college_skills =$this->getRequest()->getPost('txt_college_skills');  
            $student_comment =$this->getRequest()->getPost('txt_student_comment');

            $sub_date = date('Y-m-d');
            $username = $container->id;

            $sql = new Sql($dba);
            $insert = $sql->insert('task');
            //mentor_comment	
            $newData = array('sid'=> $username,
                'week_id' => $wid,
                'task_performed'=> $task_performed,
                'new_skills'=>  $new_skills,  
                'college_skills'=> $college_skills,
                'student_comment' => $student_comment,
                'mentor_comment'  =>'',
                'sub_date' => $sub_date,
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
    
     public function updateweek ($taskid)  
     {
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 2){
            //  echo 'taskid ' .$taskid;
            $data = array(
            'task_performed'  => $this->getRequest()->getPost('txt_task_performed'),
            'new_skills'  => $this->getRequest()->getPost('txt_new_skills'),
            'college_skills'  => $this->getRequest()->getPost('txt_college_skills'),
            'student_comment'  => $this->getRequest()->getPost('txt_student_comment')               
            );
            //   $username = $container->id;
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
}
