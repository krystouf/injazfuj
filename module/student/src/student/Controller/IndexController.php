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
        $container = new Container('username');
        $sm =$this->getServiceLocator();
            $dba = $sm->get($container->adapter);
            $username = 263;
            $sql ="SELECT * from students,teacher,supervisor Where sid=".$username."
                   AND supervisor_id = super_id
                   AND  Teacher_id = mentor_id";
            $statement = $dba->query($sql, array(5));
            $resultSet = new ResultSet;
            $resultSet->initialize($statement);
       
            return new ViewModel(array(
                'Studentinfo' => $resultSet,
             ));
    }
    
    public function informationAction(){
        $container = new Container('username');
        $sm =$this->getServiceLocator();
        $dba = $sm->get($container->adapter);
        $username = 263;
        $sql ="SELECT * from students,teacher,supervisor Where sid=".$username."
               AND supervisor_id = super_id
               AND  Teacher_id = mentor_id";
        $statement = $dba->query($sql, array(5));
        $resultSet = new ResultSet;
        $resultSet->initialize($statement);

        return new ViewModel(array(
            'Studentinfo' => $resultSet,
         ));
    }
      public function weeklyreportAction()
    {   
        return new ViewModel();
      
       
    }
            
}
