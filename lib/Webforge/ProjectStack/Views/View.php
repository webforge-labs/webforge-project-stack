<?php

namespace Webforge\ProjectStack\Views;

use Webforge\View\Renderable;

abstract class View implements Renderable {

  protected $templateIdentifier;

  protected $templateVars;

  public static function defaultTemplateName($className, $folder = '', $format = 'html', $engine = 'mustache') {
    $dashed = S::camelCaseToDash(ClassUtil::getClassName($className));

    return sprintf(':%s:%s.%s.%s', $folder, $dashed, $format, $engine);
  }

  public function __construct($templateIdentifier, $vars) {
    $this->templateIdentifier = $templateIdentifier;
    $this->templateVars = (object) $vars;
  }

  public function init() {
  }

  public function getTemplateIdentifier() {
    return $this->templateIdentifier;
  }

  public function getTemplateVariables() {
    return $this->templateVars;
  }

  /**
   * Creates a empty indication flag has{{Variable}} for {{Variable}}
   * 
   * if hasVariable already exists it is not expanded
   * if the value from Variable is empty() hasVariable is false else true
   */
  protected function expandEmptyFlag($varname) {
    $flagname = 'has'.ucfirst($varname);

    $this->expandVar($flagname, isset($this->templateVars->$varname) && !empty($this->templateVars->$varname));
  }

  protected function expandDuplicate($varname, $duplicatename) {
    $this->expandVar($varname, $this->getVar($duplicatename));
  }

  protected function getVar($varname, $default = NULL) {
    return isset($this->templateVars->$varname) ? $this->templateVars->$varname : $default;
  }

  protected function setVar($varname, $value) {
    return $this->templateVars->$varname = $value;
  }

  protected function expandVar($varname, $value) {
    if (!isset($this->templateVars->$varname)) {
      $this->templateVars->$varname = $value;
    }
  }
}
