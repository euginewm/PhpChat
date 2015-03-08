<?php

use classes\DependenciesContainer;

include 'config.php';
include 'session.php';
include 'functions.php';

// TODO: move PDO as Class and provide it into all used classes
try {
  $pdo = new PDO('mysql:host=' . $config['db']['server'] . ';dbname=' . $config['db']['database'], $config['db']['login'], $config['db']['password'], [PDO::ATTR_PERSISTENT => true]);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->exec("set names utf8");
} catch (PDOException $e) {
  echo $e->getMessage();
}


$Router = DependenciesContainer::init('Router');
$SysMessage = DependenciesContainer::init('SysMessage');
$ChatRoom = DependenciesContainer::init('ChatRoom');
$User = DependenciesContainer::init('User');
$View = DependenciesContainer::init('View');
$Chat = DependenciesContainer::init('Chat');
$ChatMessage = DependenciesContainer::init('ChatMessage');

$page_title = 'Chat';
$view_content = '';

if ($User->isLoggedIn()) {
  switch ($Router->getRequest()) {
    case '/logout':
      $User->LogOutAction();
      $Router->gotoPath('/login');
      break;

    //    case '/chat/add-message':
    //    case '/chat/create-room':


    /**
     * CRUD: C
     */
    case '/chat/api/chat/':
      if ($Router->getRequestType() == 'POST') {
        switch ($Router->getPost('action')) {
          case 'add-message':
            $ChatMessage->create();
            break;

          case 'create-room':
            $User->ChatRoom->gotoRoom($Chat->ChatRoom->create());
            break;
        }
      }

      exit();
      break;

    /**
     * CRUD: D
     */
    case '/chat/api/chat/remove-message/:int/':
      if ($Router->getRequestType() == 'DELETE' && $Chat->ChatRoom->ActionAccess->access('can remove messages')) {
        $ChatMessage->delete();
      }
      exit();
      break;
    case '/chat/api/chat/remove-room/:int/':
      if ($Router->getRequestType() == 'DELETE' && $Chat->ChatRoom->ActionAccess->access('can remove room') && $ChatRoom->delete()) {
        $ChatRoom->moveUsersAway();
      }

      exit();
      break;

    case '/chat/read-messages':
      print $View->renderJSON(['html' => $View->render('chatMessagesList')]);
      exit();
      break;

    case '/chat/read-room-users':
      print $View->renderJSON(['html' => $View->render('chatRoomUsersList')]);
      exit();
      break;

    case '/chat/change-room/:int':
      $User->ChatRoom->gotoRoom($Router->getArg('int', 0));
      exit();
      break;

    default:
      $page_title = 'Chat';
      $view_content = $View->render('chat');
      break;
  }
}
else {
  // Not logged in users
  switch ($Router->getRequest()) {
    default:
    case '/login':
      $User->LogInAction();
      $page_title = 'Login';
      $view_content = $View->render('login');
      break;

    case '/register':
      $User->SignInAction();
      $page_title = 'Register';
      $view_content = $View->render('register');
      break;
  }
}


print $View->render('main-layout', [
  'page_title' => $page_title,
  'view_content' => $view_content
]);
