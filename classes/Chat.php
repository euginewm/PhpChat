<?php

namespace classes;


use interfaces\iChat;
use interfaces\iChatRoom;

class Chat implements iChat {
  public $id = 0;
  public $rooms = [];

  /**
   * @var iChatRoom
   */
  public $ChatRoom;

  public function __construct(iChatRoom $ChatRoom) {
    $this->ChatRoom = $ChatRoom;
  }
}
