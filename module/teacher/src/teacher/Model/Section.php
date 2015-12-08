<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 namespace teacher\Model;

 class Section
 {
     public $Section_id;
     public $Section_Name;

     public function exchangeArray($data)
     {
         $this->Section_id     = (!empty($data['Section_id'])) ? $data['Section_id'] : null;
         $this->Section_Name = (!empty($data['Section_Name'])) ? $data['Section_Name'] : null;
     }
 }