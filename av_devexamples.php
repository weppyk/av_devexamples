<?php
/**
 * This module contents examples for developing module  and working with prestashop. There are many documented functionality.
 * 
 * This file inicialize module and uses for install, uninstall and configuration
 * 
 */

/** checking PS version prevents malicious visitor to load this file directly */
if (!defined('_PS_VERSION_')) {
  exit;
}


class Av_DevExamples extends Module
{

  /**use __construct for basic information about module */
  public function __construct()
  {
    $this->name='av_devexamples';
    $this->tab='back_office_features';
    $this->version='0.1.0';
    $this->author='Radek Drlik';
    //$this->need_instance=1; // This use if you need to load module class while module Modules page in BO, if set to 1 it enable Warnings - probably doesnt work in PS1.7
    $this->ps_versions_compliancy=[
        'min'=>'1.7',
        'max'=>'1.7.99',
    ];
    $this->bootstrap=true; //if true, templates file is on 1.6 version templates
    parent::__construct(); //need to be after name and before translations
    $this->displayName=$this->l('Developer Examples for Prestashop 1.7');
    $this->description=$this->l('This modules is for helping developers to learn working with Prestashop.');

    $this->confirmUninstall=$this->l('Are you sure you want to uninstall?');

    //If not in db table configuration show warnings in BO-> Module Manager -> Alerts
    if (!Configuration::get('MYMODULE_NAME')) {
        $this->warning=$this->l('No name provided');
    }
  }

  /** Use install function for register hooks, install db and use controllers in tab */
  public function install()
  {
    $this->_clearCache('*');
    // If multistore actived set module for all shops(contexts) - this will process following functions for every shop */
    if (Shop::isFeatureActive()) {
        Shop::setContext(Shop::CONTEXT_ALL);
    }
    return (
      //parent::install validate if string, check version compatibility, dependencies and if not installed, install module, enable for actual shop/context
      //set permision, add restriction for groups,update translation
      parent::install() 
      && $this->registerHook('displayAdminProductsExtra')
    );
  }

  /** Use uninstall function for unregister hooks, uninstall db and unmount controllers from tabs */
  public function uninstall()
  {
    $this->_clearCache('*');
    return (
      parent::uninstall()
      && $this->unregisterHook('displayAdminProductsExtra')
    );
  }

}

