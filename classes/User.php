<?php

namespace classes;

use interfaces\iChatRoom;
use interfaces\iRouter;
use interfaces\iSysMessage;
use interfaces\iUser;
use PDO;

class User implements iUser {
  public $login = '';
  public $email = '';
  public $status = true;

  /**
   * @var iRouter
   */
  private $Router;

  /**
   * @var iSysMessage
   */
  private $SysMessage;

  /**
   * @var iChatRoom
   */
  public $ChatRoom;

  public function __construct(iRouter $Router, iSysMessage $SysMessage, iChatRoom $ChatRoom) {
    $this->Router = $Router;
    $this->SysMessage = $SysMessage;
    $this->ChatRoom = $ChatRoom;
  }

  public function LogInAction() {
    $this->Router->checkCSRFProtection();

    $username = $this->Router->getPost('username');
    $password = $this->Router->getPost('password');

    if (!$username || !$password) {
      $this->SysMessage->set("Необходимо ввести логин и пароль!");

    }
    else {
      $this->LogInProcess($username, $password);
    }

  }

  public function LogOutAction() {
    session_destroy();
  }

  public function SignInAction() {
    $this->Router->checkCSRFProtection();
    $username = $this->Router->getPost('username');
    $password = $this->Router->getPost('password');
    $confirm_password = $this->Router->getPost('confirm_password');

    if (!$username || !$password) {
      $this->SysMessage->set("Необходимо ввести логин и пароль!");
    }
    elseif ($password != $confirm_password) {
      $this->SysMessage->set("Пароли должны совпадать");
    }
    else {
      $this->SignInProcess($username, _hash($password));
    }
  }

  public function ChangePasswordAction() {
    // TODO: Implement ChangePasswordAction() method.
  }

  public function ChangeNameAction() {
    // TODO: Implement ChangeNameAction() method.
  }

  public function ChangeEmailAction() {
    // TODO: Implement ChangeEmailAction() method.
  }

  public function isLoggedIn() {
    // TODO: Implement isLoggedIn() method.
    return !empty($_SESSION['user_data']['username']);
  }

  public function findValidateUser($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT u.id, u.username, u.password, ur.role_id, ur.user_id FROM user u LEFT JOIN user_role ur ON u.id=ur.user_id WHERE u.username=? AND u.password=? LIMIT 0,1');
    $stmt->execute([$username, $password]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function LogInProcess($username, $password) {
    if (!$UserData = $this->findValidateUser($username, _hash($password))) {
      $this->SysMessage->set("Неверный логин или пароль!");
    }
    else {
      if (empty($UserData['user_id'])) {
        $UserData['user_id'] = (int) $UserData['id'];
        $UserData['role_id'] = 0;
      }
      $UserData['room_id'] = 1;

      $_SESSION['user_data'] = $UserData;

      $this->ChatRoom->gotoRoom(1);

      $this->Router->gotoPath('/chat');
    }
  }

  public function SignInProcess($username, $password) {
    if ($UserData = $this->findValidateUser($username, $password)) {
      $this->SysMessage->set("Такой логин уже занят!");
    }
    else {
      global $pdo;
      $stmt = $pdo->prepare('INSERT INTO user (username, password) VALUES(?,?)');
      $stmt->execute([$username, _hash($password)]);

      $this->LogInProcess($username, $password);
    }
  }

  public function findUser() {
    global $pdo;
    $stmt = $pdo->prepare('SELECT u.id, u.username, u.password, ur.role_id, ur.user_id FROM user u LEFT JOIN user_role ur ON u.id=ur.user_id WHERE u.id=? LIMIT 0,1');
    $stmt->execute([$_SESSION['user_data']['user_id']]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
      $this->SysMessage->set("Этот пользователь наверное заблокирован или удален!");
    }
    else {
      if (empty($UserData['user_id'])) {
        $UserData['user_id'] = (int) $UserData['id'];
        $UserData['role_id'] = 0;
      }
      $_SESSION['user_data'] = $UserData;
    }
  }

  public function getUserNameByID($user_id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT u.username FROM user u WHERE u.id=?');
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
  }
}
