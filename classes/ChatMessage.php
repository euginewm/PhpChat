<?php

namespace classes;


use interfaces\iChatMessage;
use interfaces\iRouter;

class ChatMessage implements iChatMessage {
  public $user_id = 0;
  public $body = '';
  public $status = true;

  private $Router;

  public function __construct(iRouter $Router) {
    $this->Router = $Router;
  }

  public function create() {

    if (!$recipient_id = $this->Router->getPost('recipient_id')) {
      $recipient_id = 0;
    }

    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO message (user_id, message, type, recipient_id) VALUES (?,?,?,?)');
    $stmt->execute([
                     $_SESSION['user_data']['id'],
                     $this->Router->getPost('message'),
                     ($recipient_id) ? 'private' : 'public',
                     $recipient_id,
                   ]);

    $message_id = $pdo->lastInsertId('id');

    $stmt = $pdo->prepare('INSERT INTO message_room (message_id, room_id) VALUES(?,?)');

    $room_id = !empty($_SESSION['user_data']['room_id']) ? $_SESSION['user_data']['room_id'] : 1;

    $stmt->execute([$message_id, $room_id]);
  }

  public function read() {
    // TODO: Implement read() method.
  }

  public function update() {
    // TODO: Implement update() method.
  }

  public function delete() {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE message m SET `status`=? WHERE m.id=?');
    $stmt->execute(['disabled', $this->Router->getArg('int', 0)]);
    return;
  }

  public function getMessageOwnerId($message_id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT user_id FROM message m WHERE m.id=?');
    $stmt->execute([$message_id]);
    return $stmt->fetchColumn();
  }
}
