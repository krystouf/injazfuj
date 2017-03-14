<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace admin\Controller;

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
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        
        $secsql ="SELECT * FROM section";
        $secstatement = $dba->query($secsql, array(5));
        $section = new ResultSet;
        $section->initialize($secstatement);
        
        $teasql ="SELECT * FROM teacher";
        $teastatement = $dba->query($teasql, array(5));
        $teacher = new ResultSet;
        $teacher->initialize($teastatement);
        
        if ($auth->hasIdentity() && $container->type == 0) {
            if($this->getRequest()->getPost('submit-update')){
                $count = (int) $this->getRequest()->getPost('rcount');
                for ($i =1 ; $i <= $count; $i++){
                    $id= $this->getRequest()->getPost('rid'.$i);
                    $att= $this->getRequest()->getPost('rattendance'.$i);
                    $com= $this->getRequest()->getPost('rcomment'.$i);
                    $stup= $this->getRequest()->getPost('status-update'.$i);
                    if ($stup != 0){
                        $data = array(
                            'counted'  => $att,
                            'comment'  => $com,
                            'Abs_value' => $stup
                        );
                        $sql = new Sql($dba);
                        $update = $sql->update();
                        $update->table('attendance');
                        $update->set($data);
                        $update->where(array('Att_id' => $id));
                        $statement = $sql->prepareStatementForSqlObject($update);
                        $statement->execute();
                    }else{
                        $id= $this->getRequest()->getPost('rid'.$i);
                        $sql = new Sql($dba);
                        $delete = $sql->delete('attendance')->where(array('Att_id' => $id));
                        $statement = $sql->prepareStatementForSqlObject($delete);
                        $statement->execute();
                    }
                }
                $sday = $this->getRequest()->getPost('sdate-filter');
                $eday = $this->getRequest()->getPost('edate-filter');
                $syour_date = date("Y-m-d", strtotime($sday));
                $eyour_date = date("Y-m-d", strtotime($eday));
                return new ViewModel(array(
                     'attendance' => $this->getRepport($syour_date, $eyour_date, $this->getRequest()->getPost('stid-filter'), $this->getRequest()->getPost('tea-filter'), $this->getRequest()->getPost('sec-filter'), $this->getRequest()->getPost('p-filter'), $this->getRequest()->getPost('status-filter')),
                     'sections' => $section,
                     'teachers' => $teacher,
                     'message' => "Attendance report updated",
                     'sday' => $sday,
                     'eday' => $eday,
                     'stid' => $this->getRequest()->getPost('stid-filter'),
                     'tid' => $this->getRequest()->getPost('tea-filter'),
                     'secid' => $this->getRequest()->getPost('sec-filter'),
                     'pid' => $this->getRequest()->getPost('p-filter'),
                     'status' => $this->getRequest()->getPost('status-filter'),
                     'filter' => "Attendance report for ".$this->getRequest()->getPost('stid-filter')." from ".date("l jS \of F Y ", strtotime($sday))." to ".date("l jS \of F Y ", strtotime($eday)),
                ));
            }else if($this->getRequest()->getPost('submit-date')){
                $sday = $this->getRequest()->getPost('sdate-filter');
                $eday = $this->getRequest()->getPost('edate-filter');
                $syour_date = date("Y-m-d", strtotime($sday));
                $eyour_date = date("Y-m-d", strtotime($eday));
                return new ViewModel(array(
                     'attendance' => $this->getRepport($syour_date, $eyour_date, $this->getRequest()->getPost('stid-filter'), $this->getRequest()->getPost('tea-filter'), $this->getRequest()->getPost('sec-filter'), $this->getRequest()->getPost('p-filter'), $this->getRequest()->getPost('status-filter')),
                     'sections' => $section,
                     'teachers' => $teacher,
                     'message' => "",
                     'sday' => $sday,
                     'eday' => $eday,
                     'stid' => $this->getRequest()->getPost('stid-filter'),
                     'tid' => $this->getRequest()->getPost('tea-filter'),
                     'secid' => $this->getRequest()->getPost('sec-filter'),
                     'pid' => $this->getRequest()->getPost('p-filter'),
                     'status' => $this->getRequest()->getPost('status-filter'),
                     'filter' => "Attendance report for ".$this->getRequest()->getPost('stid-filter')." from ".date("l jS \of F Y ", strtotime($sday))." to ".date("l jS \of F Y ", strtotime($eday)),
                ));
            }else{
                return new ViewModel(array(
                     'attendance' => $this->getRepport(date('Y-m-d'), date('Y-m-d'), "", 0, 0, 0, 0),
                     'sections' => $section,
                     'teachers' => $teacher,
                     'message' => "",
                     'sday' => date('m/d/Y'),
                     'eday' => date('m/d/Y'),
                     'stid' => "",
                     'tid' => 0,
                     'secid' => 0,
                     'pid' => 0,
                     'status' => 0,
                     'filter' => "Attendance report for ". date("l jS \of F Y ", strtotime(date('m/d/Y'))),
                ));
            }
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function getRepport($sday, $eday, $sid, $tid, $secid, $pid, $status){
        $sm =$this->getServiceLocator();
        $container = new Container('username');
        $dba = $sm->get($container->adapter);
        $sql = "SELECT * 
        FROM attendance, students, teacher, section
        WHERE attendance.St_Id=students.sid
        AND attendance.teacher=teacher.Teacher_id
        And students.Student_Section=section.Section_id";
        if($sid != ""){
            $sql=$sql." AND students.Student_id='".$sid."'";
        }
        
        if($tid != 0){
            $sql=$sql." AND attendance.teacher='".$tid."'";
        }
        
        if($secid != 0){
            $sql=$sql." AND students.Student_Section='".$secid."'";
        }
        
        if($pid != 0){
            $sql=$sql." AND attendance.Abs_period='".$pid."'";
        }
        
        if($status != 0){
            $sql=$sql." AND attendance.Abs_value='".$status."'";
        }
        $sql=$sql." AND attendance.Abs_Day BETWEEN '".$sday."' AND '".$eday."' ORDER BY students.Student_Section ASC, students.Student_Name ASC, attendance.Abs_period ASC";

        $statement = $dba->query($sql, array(5));
        $resultSet = new ResultSet;
        $resultSet->initialize($statement);
        
        return $resultSet;
    }

    public function attendanceAction()
    {
        return new ViewModel();
    }

    public function studentsAction()
    {
        $auth = new AuthenticationService();
        $container = new Container('username');
        if ($auth->hasIdentity() && $container->type == 0){
            $secid = 1;
            if ($this->params()->fromQuery('secid')){
                $secid = $this->params()->fromQuery('secid');
            }
            $sm =$this->getServiceLocator();
            $dba = $sm->get($container->adapter);
            $sql ="SELECT * FROM section";
            $statement = $dba->query($sql, array(5));
            
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            
            $sql2 = "SELECT students.*, 
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
                    from students LEFT JOIN attendance on students.sid=attendance.St_Id
                    WHERE students.Student_Section=".$secid."
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
    
    public function addattendanceAction(){
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 0){
            if($this->getRequest()->getPost('submit-but')){
                $per = $this->getRequest()->getPost('addp');
                $tea = $this->getRequest()->getPost('addt');
                $day = $this->getRequest()->getPost('addd');
                $subject = $this->getSubject($tea);
                $count = (int) $this->getRequest()->getPost('stcount');
                $starray = $count;
                $timestamp = date('H:i:s');
                for ($i =1 ; $i <= $count; $i++){
                    $statt= $this->getRequest()->getPost('attendance'.$i);
                    $stid= $this->getRequest()->getPost('idstudent'.$i);
                    if ($statt != 0) {
                        $sql = new Sql($dba);
                        $insert = $sql->insert('attendance');
                        $newData = array('St_Id'=> $stid,
                            'Abs_Day' => date("Y-m-d", strtotime($day)),
                            'Abs_period'=> $per,
                            'Abs_value'=> $statt,  
                            'att_time'=> $timestamp,
                            'teacher' => $tea,
                            'subject' => $subject,
                        );
                        $insert->values($newData);
                        $Query = $sql->getSqlStringForSqlObject($insert);
                        $statement = $dba->query($Query);
                        $statement->execute();
                    }
                }
                return new ViewModel(array(
                    'Step' => "3",
                ));
            }else{
                if($this->getRequest()->getPost('next-but')){
                    $sm =$this->getServiceLocator();
                    $dba = $sm->get($container->adapter);
                    $tablegetaway = new TableGateway('students', $dba);
                    $rowset = $tablegetaway->select(function(Select $select){
                        $select->where(array('Student_Section'=> (int)$this->getRequest()->getPost('secadd')));
                    });
                    $students = $rowset->toArray();
                    
                    $addday = $this->getRequest()->getPost('addday');
                    return new ViewModel(array(
                        'students' => $students,
                        'Step' => "2",
                        'wradday' => date("l jS \of F Y ", strtotime($addday)),
                        'adday' => $addday,
                        'period' => $this->getRequest()->getPost('periodadd'),
                        'teacher' => $this->getRequest()->getPost('teaadd'),
                        'section' => $this->getRequest()->getPost('secadd'),
                    ));
                }else{
                    $secsql ="SELECT * FROM section";
                    $secstatement = $dba->query($secsql, array(5));
                    $section = new ResultSet;
                    $section->initialize($secstatement);

                    $teasql ="SELECT * FROM teacher";
                    $teastatement = $dba->query($teasql, array(5));
                    $teacher = new ResultSet;
                    $teacher->initialize($teastatement);
                    return new ViewModel(array(
                        'Step' => "1",
                        'day' => date('m/d/Y'),
                        'sections' => $section,
                        'teachers' => $teacher,
                    ));
                }
            }
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
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
    
    public function deleteAction(){
        $auth = new AuthenticationService();
        $container = new Container('username');
        if ($auth->hasIdentity() && $container->type == 0){
            if($this->getRequest()->getPost('yes-but')){
                $sm =$this->getServiceLocator();
                $dba = $sm->get($container->adapter);
                $sql = new Sql( $dba );
                $delete = $sql->delete('attendance')->where("St_Id =".$this->params()->fromQuery('stid'));
                $deleteString = $sql->getSqlStringForSqlObject($delete);
                $statement = $dba->query($deleteString);
                $statement->execute();
                
                $delete = $sql->delete('students')->where("sid =".$this->params()->fromQuery('stid'));
                $deleteString = $sql->getSqlStringForSqlObject($delete);
                $statement = $dba->query($deleteString);
                $statement->execute();
                
                $this->redirect()->toRoute('adminstudents', array(), array(
                    'query' => array(
                        'secid' => $this->params()->fromQuery('secid'),
                    ),
                ));
            }else if($this->getRequest()->getPost('no-but')){
                $this->redirect()->toRoute('adminstudents', array(), array(
                    'query' => array(
                        'secid' => 1,
                    ),
                ));
            }else{
                return new ViewModel(array(
                    'stid' => $this->params()->fromQuery('stid'),
                    'secid' => $this->params()->fromQuery('secid'),
                    'stname' => $this->params()->fromQuery('stname'),
                ));
            }
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function workplacementAction(){
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 0){
            $secid = 1;
            if ($this->params()->fromQuery('secid')){
                $secid = $this->params()->fromQuery('secid');
            }
            
            $sql1 ="SELECT * FROM section WHERE workplacement=1";
            $statement1 = $dba->query($sql1, array(5));
            $resultSet1 = new ResultSet;
            $resultSet1->initialize($statement1);
            
            $sql2 ="SELECT * FROM students";
            $statement2 = $dba->query($sql2, array(5));
            $resultSet2 = new ResultSet;
            $resultSet2->initialize($statement2);
            return new ViewModel(array(
                'sections' => $resultSet1,
                'secid' => $secid,
                'students' => $resultSet2
            ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function workplacesAction(){
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 0){
            if($this->getRequest()->getPost('add-cmp')){
                $cmpname= $this->getRequest()->getPost('new-cmp-name');
                if ($cmpname != "") {
                    $sql = new Sql($dba);
                    $insert = $sql->insert('companies');
                    $newData = array('Company_Name' => $cmpname,
                    );
                    $insert->values($newData);
                    $Query = $sql->getSqlStringForSqlObject($insert);
                    $statement = $dba->query($Query);
                    $statement->execute();
                }
            }
            $sql ="SELECT * FROM supervisor, companies "
                    . "Where supervisor.Company_ID=companies.Company_ID "
                    . "Group by Company_Name, super_name";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            return new ViewModel(array(
                'companies' => $resultSet,
            ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function supervisorsAction(){
        date_default_timezone_set('Asia/Dubai');
        $auth = new AuthenticationService();
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        if ($auth->hasIdentity() && $container->type == 0){
            return new ViewModel();
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
}