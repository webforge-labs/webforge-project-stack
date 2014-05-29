<?php

namespace Webforge\ProjectStack\Test;

class FOSUserProcessor implements \Nelmio\Alice\ProcessorInterface {

  protected $userManager;

  public function __construct($userManager) {
    $this->userManager = $userManager;
  }

  public function preProcess($data) {
    if ($data instanceof FOSUserData) {
      $user = $this->userManager->createUser();
      $user->setUsername($data->username);
      $user->setEmail($data->email);
      $user->setPlainPassword($data->plainPassword);
      $user->setEnabled($data->enabled);
      $user->setRoles($data->roles);
      $this->userManager->updateUser($user, $andFlush = FALSE);
      return $user;
    }

    return $data;
  }

  public function postProcess($object) {
    return $object;
  }
}
