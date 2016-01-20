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
    
    public function indexAction(){
        $auth = new AuthenticationService();
        $container = new Container('username');
        if ($auth->hasIdentity() && $container->type == 1){
            $sm =$this->getServiceLocator();
            $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
            $username = $container->id;
            $sql ="SELECT section FROM teacher_section WHERE computer_teacher=".$username." OR wk_teacher=".$username." OR english_teacher=".$username." OR math_teacher=".$username." OR arabic_teacher=".$username." ORDER BY section ASC";
            $statement = $dbAdpater->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $sql2 ="SELECT * from teacher Where Teacher_id=".$username;
            $statement2 = $dbAdpater->query($sql2, array(5));
            $resultSet2 = new ResultSet;
            $resultSet2->initialize($statement2);
            return new ViewModel(array(
                'sections' => $resultSet,
                'teacher' => $resultSet2,
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
        return $period;
    }

    public function getSubject($username){
        $sm =$this->getServiceLocator();
        $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
        $sql ="SELECT Teacher_Subject FROM teacher WHERE Teacher_id=".$username;
        $statement = $dbAdpater->query($sql, array(5));
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
                $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
                $starray = $count;
                for ($i =1 ; $i <= $count; $i++){
                    $statt= $this->getRequest()->getPost('attendance'.$i);
                    $stid= $this->getRequest()->getPost('idstudent'.$i);
                    $period = $this->getPeriod();
                    date_default_timezone_set('Asia/Dubai');
                    $timestamp = date('H:i:s');
                    if ($period != "Break" && $statt != 0) {
                        $sql = new Sql($dbAdpater);
                        $insert = $sql->insert('attendance');
                        $newData = array('St_Id'=> $stid,
                            'Abs_Day' => date('Y-m-d'),
                            'Abs_period'=> $period,
                            'Abs_value'=> $statt,  
                            'att_time'=> $timestamp,
                            'teacher' => $container->id,
                            'subject' => $this->getSubject($container->id),
                        );
                        $insert->values($newData);
                        $Query = $sql->getSqlStringForSqlObject($insert);
                        $statement = $dbAdpater->query($Query);
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
                    $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
                    $tablegetaway = new TableGateway('students', $dbAdpater);
                    $rowset = $tablegetaway->select(function(Select $select){
                        $select->where(array('Student_Section'=> (int)$this->getRequest()->getPost('section')));
                    });
                    $students = $rowset->toArray();
                    return new ViewModel(array(
                        'students' => $students,
                        'Step' => "2",
                        'period' => $this->getPeriod(),
                    ));
                }else{
                    $sm =$this->getServiceLocator();
                    $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
                    $username = $container->id;
                    $sql ="SELECT section FROM teacher_section WHERE computer_teacher=".$username." OR wk_teacher=".$username." OR english_teacher=".$username." OR math_teacher=".$username." OR arabic_teacher=".$username." ORDER BY section ASC";
                    $statement = $dbAdpater->query($sql, array(5));
                    $resultSet = new ResultSet;
                    $resultSet->initialize($statement);
                    return new ViewModel(array(
                        'sections' => $resultSet,
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
            $sm =$this->getServiceLocator();
            $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
            $username = $container->id;
            $sql ="SELECT section FROM teacher_section WHERE computer_teacher=".$username." OR wk_teacher=".$username." OR english_teacher=".$username." OR math_teacher=".$username." OR arabic_teacher=".$username;
            $statement = $dbAdpater->query($sql, array(5));
            $statement2 = $dbAdpater->query($sql, array(5));
            
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $resultSet2 = new ResultSet;
            $resultSet2->initialize($statement2);
            
            $sql = "SELECT students.* FROM students, teacher_section
            WHERE students.Student_Section=teacher_section.section
            AND (teacher_section.computer_teacher=".$username." 
            OR teacher_section.wk_teacher=".$username." 
            OR teacher_section.english_teacher=".$username."
            OR teacher_section.math_teacher=".$username." 
            OR teacher_section.arabic_teacher=".$username.")";
            
            $statement3 = $dbAdpater->query($sql, array(5));
            $resultSet3 = new ResultSet;
            $resultSet3->initialize($statement3);
            $resultSet3->buffer();
            return new ViewModel(array(
                'sections' => $resultSet,
                'sections2' => $resultSet2,
                'students' => $resultSet3,
             ));
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
}
