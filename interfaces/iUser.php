<?php

namespace interfaces;


interface iUser {
  public function LogInAction();

  public function LogInProcess($username, $password);

  public function SignInProcess($username, $password);

  public function LogOutAction();

  public function SignInAction();

  public function ChangePasswordAction();

  public function ChangeNameAction();

  public function ChangeEmailAction();

  public function isLoggedIn();

  public function findValidateUser($username, $password);
}
