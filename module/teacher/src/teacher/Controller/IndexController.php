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

    public function attendanceAction()
    {
        // INSERT INTO `injaz`.`attendance` (`St_Id`, `Abs_Day`, `Abs_period`, `Abs_value`) VALUES ('', '', '', '');
        $activeSec = null;
        if($this->getRequest()->getPost('submit-but')){
            $count = (int)$this->getRequest()->getPost('stcount');
                
            
            
            $sm =$this->getServiceLocator();
                $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter');
                
                
                $sql = new Sql($dbAdpater);
                $insert = $sql->insert('attendance');
                $newData = array(
                    'St_Id'=> 'h2051',
                    'Abs_Day'=> 1,
                    'Abs_period'=> 1,
                    'Abs_value'=> 1
                    
                    );
                $insert->values($newData);
                 $Query = $sql->getSqlStringForSqlObject($insert);
                 $statement = $dbAdpater->query($Query);
                 $statement->execute();
             //   $sql = "INSERT INTO attendance ('St_Id', 'Abs_Day', 'Abs_period', 'Abs_value') VALUES ('3', 1, 1, 1)";
            //    $statement = $dbAdpater->query($sql);
          //      $statement->execute();
            
            return new ViewModel(array(
                 'att' => $count,
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
                ));
            }else{
                return new ViewModel(array(
                    'sections' => $this->getSectionTable()->fetchAll(),
                    'Step' => "1",
                ));
            }
        }
        
        
        
    }
    
    
}
