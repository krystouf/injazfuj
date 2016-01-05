<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace admin\Controller;

<<<<<<< HEAD
=======
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Sql\Sql;
use Zend\View\Model\ViewModel;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
>>>>>>> origin/master

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Sql\Sql;
use Zend\View\Model\ViewModel;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $auth = new AuthenticationService();
        $container = new Container('username');
        if ($auth->hasIdentity() && $container->type == 0) {
            return new ViewModel();
        }else{
            return $this->redirect()->toRoute('login',
            array('controller'=>'index',
                'action' => 'login'));
        }
    }
    
    public function attendanceAction()
    {
        $sm =$this->getServiceLocator();
        $dbAdpater = $sm->get('Zend\Db\Adapter\Adapter'); 
        $name ="Qudah";
        $sql="";
     //   $resultSet = $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        $sql = "SELECT * FROM attendance ";
        $resultSet = $dbAdpater->query($sql);
        $resultSet->execute();
  
         $att = $resultSet->toArray();
                foreach ($att as $attrow){
                 $name =$attrow['St_Id'];
                }
        /*
           $tablegetaway = new TableGateway('attendance', $dbAdpater);
           $rowset = $tablegetaway->select(function(Select $select){
      //     $select->where(array('Abs_period'=> 2 ));
                });
                $att = $rowset->toArray();
                foreach ($att as $attrow){
                 $name =$attrow['St_Id'];
                }
         * 
         */
        return new ViewModel(array(
            'name'=>$name,
        ));
    }

}
