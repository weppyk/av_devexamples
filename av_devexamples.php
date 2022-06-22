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
      && $this->registerHook('displayLeftColumn')
      && $this->registerHook('displayRightColumn')
      && $this->registerHook('displayContentWrapperTop')
      && $this->registerHook('actionFrontControllerSetMedia')
    );
  }

  /** Use uninstall function for unregister hooks, uninstall db and unmount controllers from tabs */
  public function uninstall()
  {
    $this->_clearCache('*');
    return (
      parent::uninstall()
      && $this->unregisterHook('leftColumn')
      && $this->unregisterHook('rightColumn')
      && $this->unregisterHook('displayContentWrapperTop')
      && $this->unregisterHook('actionFrontControllerSetMedia')
    );
  }

  /** Configuration site */
  public function getContent()
  {
    return "funny ".$this->displayFormConfigDevExamples() ;
  }

/**
 * Builds the configuration form
 * @return string HTML code
 */
public function displayFormConfigDevExamples()
{
  // Init Fields form array
  $form = [
    'form' => [
      'legend' => [
        'title' => $this->l('Settings'),
      ],
      'input' => [
        [
          'type' => 'text',
          'label' => $this->l('Configuration value'),
          'name' => 'MYMODULE_CONFIG',
          'size' => 20,
          'required' => true,
        ],
      ],
      'submit' => [
        'title' => $this->l('Save'),
        'class' => 'btn btn-default pull-right',
      ],
    ],
  ];

  $helper = new HelperForm();

  // Module, token and currentIndex
  $helper->table = $this->table;
  $helper->name_controller = $this->name;
  $helper->token = Tools::getAdminTokenLite('AdminModules');
  $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
  $helper->submit_action = 'submit' . $this->name;

  // Default language
  $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

  // Load current value into the form
  $helper->fields_value['MYMODULE_CONFIG'] = Tools::getValue('MYMODULE_CONFIG', Configuration::get('MYMODULE_CONFIG'));

  return $helper->generateForm([$form]);
}

public function hookDisplayLeftColumn($params)
{
    $this->context->smarty->assign([
        'my_module_name' => Configuration::get('MYMODULE_NAME'),
        'my_module_link' => $this->context->link->getModuleLink('av_devexamples', 'display')
    ]);

    return "Here follows template: ".$this->display(__FILE__, 'av_devexamples.tpl');
}

public function hookDisplayRightColumn($params)
{
  return $this->hookDisplayLeftColumn($params);
}
public function hookdisplayContentWrapperTop($params)
{
  return $this->hookDisplayLeftColumn($params);
}

public function hookActionFrontControllerSetMedia()
{
    $this->context->controller->registerStylesheet(
        'av_devexamples-style',
        $this->_path.'views/css/av_devexamples.css',
        [
            'media' => 'all',
            'priority' => 1000,
        ]
    );

    $this->context->controller->registerJavascript(
        'av_devexamples-javascript',
        $this->_path.'views/js/av_devexamples.js',
        [
            'position' => 'bottom',
            'priority' => 1000,
        ]
    );
}

}

