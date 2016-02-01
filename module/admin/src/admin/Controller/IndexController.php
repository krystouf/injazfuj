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
        $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
        
        $secsql ="SELECT * FROM section";
        $secstatement = $dbAdpater->query($secsql, array(5));
        $section = new ResultSet;
        $section->initialize($secstatement);
        
        $teasql ="SELECT * FROM teacher";
        $teastatement = $dbAdpater->query($teasql, array(5));
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
                        $sql = new Sql($dbAdpater);
                        $update = $sql->update();
                        $update->table('attendance');
                        $update->set($data);
                        $update->where(array('Att_id' => $id));
                        $statement = $sql->prepareStatementForSqlObject($update);
                        $statement->execute();
                    }else{
                        $id= $this->getRequest()->getPost('rid'.$i);
                        $sql = new Sql($dbAdpater);
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
        $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
        $sql = "SELECT * 
        FROM attendance, students, teacher
        WHERE attendance.St_Id=students.sid
        AND attendance.teacher=teacher.Teacher_id";
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

        $statement = $dbAdpater->query($sql, array(5));
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
            $sm =$this->getServiceLocator();
            $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
            $sql ="SELECT * FROM section";
            $statement = $dbAdpater->query($sql, array(5));
            $statement2 = $dbAdpater->query($sql, array(5));
            
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $resultSet2 = new ResultSet;
            $resultSet2->initialize($statement2);
            
            $sql2 = "SELECT students.* FROM students";
            
            $statement3 = $dbAdpater->query($sql2, array(5));
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
