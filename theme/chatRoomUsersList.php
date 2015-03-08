<?php

use classes\DependenciesContainer;

$Chat = DependenciesContainer::init('Chat');

?>
<style>
  .active {

  }

  .disabled {
    text-decoration : line-through;
  }
</style>
<?php foreach ((array) $Chat->ChatRoom->getRoomList() as $RoomItem): ?>
  <ul class="list-group">
    <li class="list-group-item room-name" data-id="<?php print $RoomItem['id']; ?>">
      <a href="#" class="list-group-item <?php print $RoomItem['status']; ?>">
        <?php print $RoomItem['name']; ?>
        <?php if ($Chat->ChatRoom->ActionAccess->access('can remove room')): ?>
          <span class="badge remove-room" data-id="<?php print $RoomItem['id'] ?>">Remove</span>
        <?php endif; ?>
      </a>
      <ul class="list-group">
        <?php foreach ((array) $Chat->ChatRoom->getRoomUsers($RoomItem['id']) as $RoomUser): ?>
          <li class="list-group-item">
            <a href="#" class="list-group-item">
              <?php print $RoomUser['username']; ?>
              <span class="badge send-private-message" data-id="<?php print $RoomUser['id']; ?>" data-name="<?php print $RoomUser['username']; ?>">private</span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </li>
  </ul>
<?php endforeach; ?>
