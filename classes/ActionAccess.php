<?php

namespace classes;


use interfaces\iActionAccess;

class ActionAccess implements iActionAccess {

  public $rules = [
    'view disabled room-messages' => [
      'or' => [
        'role' => 1, // admin
        'isMessageOwner' => true,
      ],
    ],
    'can remove messages' => [
      'and' => [
        'role' => 1,
      ],
    ],
    'can remove room' => [
      'and' => [
        'role' => 1,
      ],
    ],

    'view disabled room' => [
      'and' => [
        'role' => 1,
      ],
    ],

    'can administer private messages' => [
      'and' => [
        'role' => 1,
      ],
    ],


  ];

  public function access($AccessName, $params = []) {
    foreach ($this->rules[$AccessName] as $group => $permissions) {
      switch ($group) {
        case 'or':
          foreach ($permissions as $permissionItem => $value) {
            if ($this->$permissionItem($value, $params)) {
              return TRUE;
            }
          }
          return FALSE;
          break;

        case 'and':
          foreach ($permissions as $permissionItem => $value) {
            if (!$this->$permissionItem($value, $params)) {
              return FALSE;
            }
          }
          return TRUE;
          break;
      }
    }

    return false;
  }

  private function role($role) {
    return $_SESSION['user_data']['role_id'] == $role;
  }

  private function isMessageOwner($bool, $params) {
    $ChatMessage = DependenciesContainer::init('ChatMessage');
    return $bool && ($ChatMessage->getMessageOwnerId($params['message_id']) == $_SESSION['user_data']['user_id']);
  }

}
