<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace login\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function loginAction()
    {
        if($this->getRequest()->getPost('student_log_but')){
            return $this->redirect()->toRoute('student',
            array('controller'=>'index',
                  'action' => 'index'));
        }else if($this->getRequest()->getPost('teacher_log_but')){
            return $this->redirect()->toRoute('teacher',
            array('controller'=>'index',
                  'action' => 'index'));
        }else if($this->getRequest()->getPost('admin_log_but')){
            return $this->redirect()->toRoute('admin',
            array('controller'=>'index',
                  'action' => 'index'));
        }else{
            return new ViewModel();
        }
    }

}
