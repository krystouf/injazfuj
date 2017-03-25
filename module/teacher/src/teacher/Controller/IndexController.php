<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace teacher\Controller;

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
    protected $SectionTable;
    
    public function toverviewAction(){
         $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        $username = $container->id;
            $sql ="SELECT * from students,teacher,supervisor,companies Where Teacher_id=".$username."
                   AND supervisor_id = super_id
                   AND 	supervisor.Company_ID = companies.Company_ID
                   AND  Teacher_id = mentor_id";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $resultSet->buffer();
            return new ViewModel(array(
                'info' => $resultSet,
             ));
       
    }
  
    
    
    public function indexAction(){
              
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        $username = $container->id;
        
        if ($auth->hasIdentity() && $container->type == 1){
              if($this->getRequest()->getPost('save-tprofile')){
                $temail =$this->getRequest()->getPost('t-email');
                $tmobile =$this->getRequest()->getPost('t-mobile');
                $tpass =$this->getRequest()->getPost('tpass');
                $id =$this->getRequest()->getPost('tidprofile');
                
                $config = $this->getServiceLocator()->get('Config');
                $staticSalt = $config['static_salt'];
                $md = MD5($tpass);
                $passsault = $staticSalt.$md;

                if ($tpass != ""){
                    $data = array(
                        't_e_mail' => $temail,
                        't_phone' => $tmobile,
                        'Teacher_pass' => $tpass,
                        'Teacher_salt' => $passsault,
                    );
                }else{
                    $data = array(
                        't_e_mail' => $temail,
                        't_phone' => $tmobile,
                    );
                }
                
                $sql = new Sql($dba);
                $update = $sql->update();
                $update->table('teacher');
                $update->set($data);
                $update->where(array('Teacher_id' => $id));
                $statement = $sql->prepareStatementForSqlObject($update);
                $statement->execute();
            }
            $username = $container->id;
            $sql ="SELECT * from teacher Where Teacher_id=".$username;
                
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            
            return new ViewModel(array(
                'Teachertinfo' => $resultSet
            ));            
                   
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
         
         
    }

    public function getSectionTable(){
        if (!$this->SectionTable){
            $sm = $this->getServiceLocator();
            $this->SectionTable = $sm->get('teacher\Model\SectionTable');
        }
        return $this->SectionTable;
     }

    public function getPeriod(){
        date_default_timezone_set('Asia/Dubai');
        $time = strtotime("now");
        $period =1;
        $container = new Container('username');
        if ($container->adapter == "adapter2"){
            if ($time >= strtotime("8:30") && $time <= strtotime("9:15")){
                $period = 1;
            }else if ($time >= strtotime("9:20") && $time <= strtotime("10:05")){
                    $period = 2;
            }else if ($time >= strtotime("10:10") && $time <= strtotime("10:55")){
                    $period = 3;
            }else if ($time >= strtotime("11:25") && $time <= strtotime("12:10")){
                    $period = 4;
            }else if ($time >= strtotime("12:11") && $time <= strtotime("12:55")){
                    $period = 5;
            }else if ($time >= strtotime("13:10") && $time <= strtotime("13:55")){
                    $period = 1;
            }else if ($time >= strtotime("14:00") && $time <= strtotime("14:45")){
                    $period = 2;
            }else if ($time >= strtotime("14:50") && $time <= strtotime("15:35")){
                    $period = 3;
            }else if ($time >= strtotime("16:05") && $time <= strtotime("16:50")){
                    $period = 4;
            }else if ($time >= strtotime("16:51") && $time <= strtotime("17:35")){
                    $period = 5;
            }else{
                    $period = 'Break';
            }
        }else{
            if ($time >= strtotime("8:20") && $time <= strtotime("9:05")){
                $period = 1;
            }else if ($time >= strtotime("9:10") && $time <= strtotime("9:55")){
                    $period = 2;
            }else if ($time >= strtotime("10:00") && $time <= strtotime("10:45")){
                    $period = 3;
            }else if ($time >= strtotime("11:25") && $time <= strtotime("12:10")){
                    $period = 4;
            }else if ($time >= strtotime("12:11") && $time <= strtotime("12:55")){
                    $period = 5;
            }else if ($time >= strtotime("13:20") && $time <= strtotime("14:05")){
                    $period = 1;
            }else if ($time >= strtotime("14:10") && $time <= strtotime("14:55")){
                    $period = 2;
            }else if ($time >= strtotime("15:00") && $time <= strtotime("15:45")){
                    $period = 3;
            }else if ($time >= strtotime("16:25") && $time <= strtotime("17:10")){
                    $period = 4;
            }else if ($time >= strtotime("17:11") && $time <= strtotime("17:55")){
                    $period = 5;
            }else{
                    $period = 'Break';
            }
        }
        
        return $period;
    }

    public function getSubject($username){
        $sm =$this->getServiceLocator();
        $container = new Container('username');
        $dba = $sm->get($container->adapter);
        $sql ="SELECT Teacher_Subject FROM teacher WHERE Teacher_id=".$username;
        $statement = $dba->query($sql, array(5));
        $resultSet = new ResultSet;
        $resultSet->initialize($statement);
        $sub = "";
        foreach ($resultSet as $res){
            $sub = $res['Teacher_Subject'];
        }
        return $sub ;
    }

    public function attendanceAction(){
        $auth = new AuthenticationService();
        $container = new Container('username');
        if ($auth->hasIdentity() && $container->type == 1){
            $activeSec = null;
            if($this->getRequest()->getPost('submit-but')){
                $count = (int) $this->getRequest()->getPost('stcount');
                $sm =$this->getServiceLocator();
                $dba = $sm->get($container->adapter);
                $starray = $count;
                for ($i =1 ; $i <= $count; $i++){
                    $statt= $this->getRequest()->getPost('attendance'.$i);
                    $stid= $this->getRequest()->getPost('idstudent'.$i);
                    $period = $this->getPeriod();
                    date_default_timezone_set('Asia/Dubai');
                    $timestamp = date('H:i:s');
                    if ($period != "Break" && $statt != 0) {
                        $sql = new Sql($dba);
                        $insert = $sql->insert('attendance');
                        $newData = array('St_Id'=> $stid,
                            'Abs_Day' => date('Y-m-d'),
                            'Abs_period'=> $period,
                            'Abs_value'=> $statt,  
                            'att_time'=> $timestamp,
                            'teacher' => $container->id,
                            'subject' => $container->sub,
                        );
                        $insert->values($newData);
                        $Query = $sql->getSqlStringForSqlObject($insert);
                        $statement = $dba->query($Query);
                        $statement->execute();
                    }
                }
                return new ViewModel(array(
                    'att' => $starray,
                    'Step' => "3",
                ));
            }else{
                if($this->getRequest()->getPost('next-but')){
                    $sm =$this->getServiceLocator();
                    $dba = $sm->get($container->adapter);
                    $tablegetaway = new TableGateway('students', $dba);
                    $container->sub = (int)$this->getRequest()->getPost('subject');
                    $rowset = $tablegetaway->select(function(Select $select){
                        $select->where(array('Student_Section'=> (int)$this->getRequest()->getPost('section')));
                        $select->order('Student_Name');
                    });
                    $students = $rowset->toArray();
                    return new ViewModel(array(
                        'students' => $students,
                        'Step' => "2",
                        'period' => $this->getPeriod(),
                    ));
                }else{
                    $sm =$this->getServiceLocator();
                    $dba = $sm->get($container->adapter);
                    $username = $container->id;
                    $sql ="SELECT section, Section_Name FROM teacher_section, section WHERE teacher_section.section=section.Section_id AND (teacher_section.computer_teacher=".$username." OR teacher_section.wk_teacher=".$username." OR teacher_section.english_teacher=".$username." OR teacher_section.math_teacher=".$username." OR teacher_section.arabic_teacher=".$username.") ORDER BY teacher_section.section ASC";
                    $statement = $dba->query($sql, array(5));
                    $resultSet = new ResultSet;
                    $resultSet->initialize($statement);
                    
                    $sql2 ="SELECT * FROM subject";
                    $statement2 = $dba->query($sql2, array(5));
                    $resultSet2 = new ResultSet;
                    $resultSet2->initialize($statement2);
                    return new ViewModel(array(
                        'sections' => $resultSet,
                        'subjects' => $resultSet2,
                        'activesub' => $this->getSubject($container->id),
                        'Step' => "1",
                        'period' => $this->getPeriod(),
                    ));
                }
            }
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function studentsAction(){
        $auth = new AuthenticationService();
        $container = new Container('username');
        if ($auth->hasIdentity() && $container->type == 1){
            $secid = 0;
            if ($this->params()->fromQuery('secid')){
                $secid = $this->params()->fromQuery('secid');
            }
            $sm =$this->getServiceLocator();
            $dba = $sm->get($container->adapter);
            $sql ="SELECT * FROM section";
            $statement = $dba->query($sql, array(5));
            
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            
            $sql2 = "SELECT students.*, section.*,
                count(case when attendance.subject=1 and attendance.Abs_value=3 and attendance.counted=1  then 1 else null end) as wk_counted, 
                count(case when attendance.subject=2 and attendance.Abs_value=3 and attendance.counted=1 then 1 else null end) as en_counted, 
                count(case when attendance.subject=3 and attendance.Abs_value=3 and attendance.counted=1 then 1 else null end) as co_counted, 
                count(case when attendance.subject=4 and attendance.Abs_value=3 and attendance.counted=1 then 1 else null end) as ma_counted, 
                count(case when attendance.subject=5 and attendance.Abs_value=3 and attendance.counted=1 then 1 else null end) as ar_counted,
                count(case when attendance.subject=1 and attendance.Abs_value=3 and attendance.counted=0  then 1 else null end) as wk_removed, 
                count(case when attendance.subject=2 and attendance.Abs_value=3 and attendance.counted=0 then 1 else null end) as en_removed, 
                count(case when attendance.subject=3 and attendance.Abs_value=3 and attendance.counted=0 then 1 else null end) as co_removed, 
                count(case when attendance.subject=4 and attendance.Abs_value=3 and attendance.counted=0 then 1 else null end) as ma_removed, 
                count(case when attendance.subject=5 and attendance.Abs_value=3 and attendance.counted=0 then 1 else null end) as ar_removed,
                count(case when attendance.subject=1 and attendance.Abs_value=1 and attendance.counted=1 then 1 else null end) as wk_countedl, 
                count(case when attendance.subject=2 and attendance.Abs_value=1 and attendance.counted=1 then 1 else null end) as en_countedl, 
                count(case when attendance.subject=3 and attendance.Abs_value=1 and attendance.counted=1 then 1 else null end) as co_countedl, 
                count(case when attendance.subject=4 and attendance.Abs_value=1 and attendance.counted=1 then 1 else null end) as ma_countedl, 
                count(case when attendance.subject=5 and attendance.Abs_value=1 and attendance.counted=1 then 1 else null end) as ar_countedl,
                count(case when attendance.subject=1 and attendance.Abs_value=1 and attendance.counted=0 then 1 else null end) as wk_removedl, 
                count(case when attendance.subject=2 and attendance.Abs_value=1 and attendance.counted=0 then 1 else null end) as en_removedl, 
                count(case when attendance.subject=3 and attendance.Abs_value=1 and attendance.counted=0 then 1 else null end) as co_removedl, 
                count(case when attendance.subject=4 and attendance.Abs_value=1 and attendance.counted=0 then 1 else null end) as ma_removedl, 
                count(case when attendance.subject=5 and attendance.Abs_value=1 and attendance.counted=0 then 1 else null end) as ar_removedl
                from students LEFT JOIN attendance on students.sid=attendance.St_Id, section 
                WHERE students.Student_Section=$secid AND students.Student_Section=section.Section_id 
                GROUP BY students.sid 
                ORDER BY students.Student_Section ASC, students.Student_Name ASC";
            
            $statement3 = $dba->query($sql2, array(5));
            $resultSet3 = new ResultSet;
            $resultSet3->initialize($statement3);
            $resultSet3->buffer();
            
            /* insert query for all students */
            
            $sqldays = "select  students.*,abs.St_Id ,count(*) as Days from students,  
                (SELECT St_Id,Abs_day,count(Att_id) FROM `attendance` 
                WHERE Abs_value=3 and counted=1
                Group by St_Id,Abs_day
                having count(Att_id) >=3 ) abs
                WHERE students.sid= abs.St_Id 
                Group by abs.St_Id";
            
            $statement4 = $dba->query($sqldays, array(5));
            $resultSet4 = new ResultSet;
            $resultSet4->initialize($statement4);
            $resultSet4->buffer();
            
            
            return new ViewModel(array(
                'sections' => $resultSet,
                'secid' => $secid,
                'students' => $resultSet3,
                'studentsAbs' => $resultSet4,
             ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
        public function teacherwpAction()
    {   
            $stid="";
            $resultSet3="";
            $container = new Container('username');
            $sm =$this->getServiceLocator();
            $dba = $sm->get($container->adapter);
            $username = $container->id;
        //students names
            $sql ="SELECT * from students Where mentor_id=".$username;   
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
        
        //weeks     
            $sql2 ="SELECT * from week";   
            $statement2 = $dba->query($sql2, array(5));
            $resultSet2 = new ResultSet;
            $resultSet2->initialize($statement2);
            
            $step=1;
        //work plan
            if($this->getRequest()->getpost('show-but') && $this->getRequest()->getpost('week')=="0")
            {
                $stid= $this->getRequest()->getPost('student');
                $sql3 ="SELECT * from workplan  Where sid=".$stid;
                $statement3 = $dba->query($sql3, array(5));
                $resultSet3 = new ResultSet;
                $resultSet3->initialize($statement3);
                
                $step=2;
                    
            }
        
            
            if($this->getRequest()->getpost('show-but')&& $this->getRequest()->getpost('week')> 0)
            {
               
                    $step=3;
            }

            return new ViewModel(array(
                'Students' => $resultSet,
                'Weeks'    => $resultSet2,
                'workplans' => $resultSet3,
                'step'     =>$step,
                'stid'     =>$stid,
                
                ));
    }
}
