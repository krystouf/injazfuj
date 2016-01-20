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
        if ($auth->hasIdentity() && $container->type == 0) {
            if($this->getRequest()->getPost('submit-update')){
                $count = (int) $this->getRequest()->getPost('rcount');
                $sm =$this->getServiceLocator();
                $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
                for ($i =1 ; $i <= $count; $i++){
                        $id= $this->getRequest()->getPost('rid'.$i);
                        $att= $this->getRequest()->getPost('rattendance'.$i);
                        $com= $this->getRequest()->getPost('rcomment'.$i);
                        $data = array(
                            'counted'  => $att,
                            'comment'  => $com
                        );
                        $sql = new Sql($dbAdpater);
                        $update = $sql->update();
                        $update->table('attendance');
                        $update->set($data);
                        $update->where(array('Att_id' => $id));
                        $statement = $sql->prepareStatementForSqlObject($update);
                        $statement->execute();
                }
                $day = $this->getRequest()->getPost('date-filter');
                $your_date = date("Y-m-d", strtotime($day));
                return new ViewModel(array(
                     'attendance' => $this->getRepport($your_date),
                     'message' => "Attendance report updated",
                     'day' => $day,
                ));
            }else if($this->getRequest()->getPost('submit-date')){
                $day = $this->getRequest()->getPost('date-filter');
                $your_date = date("Y-m-d", strtotime($day));
                return new ViewModel(array(
                     'attendance' => $this->getRepport($your_date),
                     'message' => "",
                     'day' => $day,
                ));
            }else{
                return new ViewModel(array(
                     'attendance' => $this->getRepport(date('Y-m-d')),
                     'message' => "",
                     'day' => date('m/d/Y'),
                ));
            }
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function getRepport($day){
        $sm =$this->getServiceLocator();
        $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
        $sql = "SELECT * 
        FROM attendance, students, teacher
        WHERE attendance.St_Id=students.sid
        AND attendance.teacher=teacher.Teacher_id
        AND attendance.Abs_Day='".$day."'
        ORDER BY students.Student_Section ASC, students.Student_Name ASC, attendance.Abs_period ASC";

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
            $username = $container->id;
            $sql ="SELECT section FROM teacher_section";
            $statement = $dbAdpater->query($sql, array(5));
            $statement2 = $dbAdpater->query($sql, array(5));
            
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
            $resultSet2 = new ResultSet;
            $resultSet2->initialize($statement2);
            
            $sql = "SELECT students.* FROM students";
            
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
