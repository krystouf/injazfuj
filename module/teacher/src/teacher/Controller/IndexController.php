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
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function attendanceAction()
    {
        if($this->getRequest()->getPost('test-but')){
            return new ViewModel(array(
                'TeacherName' => $this->getRequest()->getPost('Tname'),
                'STattendance' => $this->getRequest()->getPost('attendance'),
                'STattendance1' => $this->getRequest()->getPost('attendance1'),
            ));
            
            //return $this->redirect()->toRoute('login',
           // array('controller'=>'index',
            //      'action' => 'login'));
        }else{
            return new ViewModel(array(
                'TeacherName' => "",
            ));
        }
    }
    
    
}
