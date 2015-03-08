<?php

namespace classes;


use interfaces\iActionAccess;
use interfaces\iChatRoom;
use interfaces\iRouter;
use PDO;

class ChatRoom implements iChatRoom {
  public $id = 0;
  public $name = '';
  public $users = [];
  public $messages = [];

  private $Router;

  public $ActionAccess;

  public function __construct(iRouter $Router, iActionAccess $ActionAccess) {
    $this->Router = $Router;
    $this->ActionAccess = $ActionAccess;
  }

  public function create() {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO room (name, type) VALUES(?,?)');
    $stmt->execute([$this->Router->getPost('name'), 'public']);
    return $pdo->lastInsertId('id');
  }

  public function read() {
    // TODO: Implement read() method.
  }

  public function update() {
    // TODO: Implement update() method.
  }

  public function delete() {
    if ($this->Router->getArg('int', 0) != 1) {
      global $pdo;
      $stmt = $pdo->prepare('UPDATE room r SET r.`status` = ? WHERE r.id = ?');
      $stmt->execute(['disabled', $this->Router->getArg('int', 0)]);
      return true;
    }
    return false;
  }

  public function moveUsersAway() {
    $room_id = $this->Router->getArg('int', 0);
    foreach ($this->getRoomUsers($room_id) as $UserItem) {
      $this->gotoRoom(1, $UserItem['user_id']);
    }
    return;
  }

  public function attachUser() {
    // TODO: Implement attachUser() method.
  }

  public function attachMessage() {
    // TODO: Implement attachMessage() method.
  }

  public function getRoomList() {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM room r WHERE r.type=?');
    $stmt->execute(['public']);
    return $this->filterActiveRooms($stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function getRoomUsers($room_id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM room_user ru LEFT JOIN user u ON ru.user_id=u.id WHERE ru.room_id=?');
    $stmt->execute([$room_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getRoomMessages($room_id) {
    global $pdo;
    $sql = <<<SQL
    SELECT m.user_id, m.message, m.status, m.type, m.recipient_id, mr.message_id, u.username FROM message m
    RIGHT JOIN message_room mr ON m.id=mr.message_id
    LEFT JOIN user u ON m.user_id=u.id
    LEFT JOIN user_role ur ON u.id=ur.user_id
    WHERE mr.room_id=? AND m.type=?
SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$room_id, 'public']);
    return $this->filterActiveMessages($stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function getPrivateMessages($conversation_user_id) {
    global $pdo;
    $sql = <<<SQL
    SELECT m.user_id, m.message, m.status, m.type, m.recipient_id, mr.message_id, u.username FROM message m
    RIGHT JOIN message_room mr ON m.id=mr.message_id
    LEFT JOIN user u ON m.user_id=u.id
    LEFT JOIN user_role ur ON u.id=ur.user_id
    WHERE (m.user_id=? OR m.recipient_id=?) AND m.type=?
SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$conversation_user_id, $conversation_user_id, 'private']);
    return $this->filterActiveMessages($stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function getAllPrivateMessages() {
    global $pdo;
    $sql = <<<SQL
    SELECT m.user_id, m.message, m.status, m.type, m.recipient_id, mr.message_id, u.username FROM message m
    RIGHT JOIN message_room mr ON m.id=mr.message_id
    LEFT JOIN user u ON m.user_id=u.id
    LEFT JOIN user_role ur ON u.id=ur.user_id
    WHERE (m.user_id<>? AND m.recipient_id<>?) AND m.type=?
SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
                     $_SESSION['user_data']['user_id'],
                     $_SESSION['user_data']['user_id'],
                     'private'
                   ]);
    return $this->filterActiveMessages($stmt->fetchAll(PDO::FETCH_ASSOC));
  }

  public function gotoRoom($room_id, $user_id = null) {
    if (empty($user_id)) {
      $user_id = $_SESSION['user_data']['id'];
      $_SESSION['user_data']['room_id'] = $room_id;
    }
    global $pdo;
    if (empty($_SESSION['user_data']['room_id'])) {
      $stmt = $pdo->prepare('INSERT INTO room_user (room_id, user_id) VALUES(?,?)');
      $stmt->execute([$room_id, $user_id]);
    }
    else {
      $stmt = $pdo->prepare('UPDATE room_user SET room_id=?, user_id=? WHERE user_id=?');
      $stmt->execute([
                       $room_id,
                       $user_id,
                       $user_id
                     ]);
    }
  }

  // TODO: Identical functions is duplicate

  public function filterActiveMessages($messages_list) {
    foreach ($messages_list as $index => $message_item) {
      if ($message_item['status'] == 'disabled'
          && !$this->ActionAccess->access('view disabled room-messages', ['message_id' => $message_item['message_id']])
      ) {
        unset($messages_list[$index]);
      }
    }
    return $messages_list;
  }

  public function filterActiveRooms($room_list) {
    foreach ($room_list as $index => $room_item) {
      if ($room_item['status'] == 'disabled'
          && !$this->ActionAccess->access('view disabled room' /*, ['room_id' => $room_item['id']]*/)
      ) {
        unset($room_list[$index]);
      }
    }
    return $room_list;
  }
}
