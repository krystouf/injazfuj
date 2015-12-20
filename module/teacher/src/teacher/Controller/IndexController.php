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

class IndexController extends AbstractActionController
{
    protected $SectionTable;
    
    public function indexAction()
    {
        return new ViewModel(array(
             'sections' => $this->getSectionTable()->fetchAll(),
         ));
    }
    
    public function getSectionTable()
     {
         if (!$this->SectionTable) {
             $sm = $this->getServiceLocator();
             $this->SectionTable = $sm->get('teacher\Model\SectionTable');
         }
         return $this->SectionTable;
     }
     
     public function getPeriod(){
         date_default_timezone_set('Asia/Dubai');
            $time = strtotime("now");

            $period =1;

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
            }else if ($time >= strtotime("13:30") && $time <= strtotime("14:15")){
                    $period = 1;
            }else if ($time >= strtotime("14:20") && $time <= strtotime("15:05")){
                    $period = 2;
            }else if ($time >= strtotime("15:10") && $time <= strtotime("15:55")){
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

    public function attendanceAction()
    {
        // INSERT INTO `injaz`.`attendance` (`St_Id`, `Abs_Day`, `Abs_period`, `Abs_value`) VALUES ('', '', '', '');
        $activeSec = null;
        if($this->getRequest()->getPost('submit-but')){
           $count = (int) $this->getRequest()->getPost('stcount');
         
       
         
        //   echo $count; 
        //   $starray = $this->getRequest()->getPost('attendance1');
            
            $sm =$this->getServiceLocator();
                $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
              
                  $starray = $count;   
         for ($i =1 ; $i <= $count; $i++)
         {
         //   echo ' <br> ' .$i; 
            $statt= $this->getRequest()->getPost('attendance'.$i);
            $stid= $this->getRequest()->getPost('student'.$i);
                if ($statt != "0") {
                
                
                    $sql = new Sql($dbAdpater);
                    $insert = $sql->insert('attendance');
                    $newData = array('St_Id'=> $stid ,
                                // 'Abs_Day'=> 1,
                                 'Abs_period'=> $this->getPeriod(),
                                 'Abs_value'=> $statt
                                 );
                    $insert->values($newData);
                    $Query = $sql->getSqlStringForSqlObject($insert);
                    $statement = $dbAdpater->query($Query);
                    $statement->execute();
                }
                 
            } // for
            
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
                return new ViewModel(array(
                    'sections' => $this->getSectionTable()->fetchAll(),
                    'Step' => "1",
                    'period' => $this->getPeriod(),
                ));
            }
        }
        
        
        
    }
    
    
}
