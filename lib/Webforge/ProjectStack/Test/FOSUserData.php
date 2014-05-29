<?php

namespace Webforge\ProjectStack\Test;

class FOSUserData {

  public $username;
  public $email;
  public $plainPassword;
  public $enabled = TRUE;
  public $roles = array();

  public function addRole($role) {
    if (!in_array($role, $this->roles)) {
      $this->roles[] = $role;
    }
  }
}
