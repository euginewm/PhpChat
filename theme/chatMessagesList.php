<?php

use classes\DependenciesContainer;

$Chat = DependenciesContainer::init('Chat');
$User = DependenciesContainer::init('User');

?>
<style>
  .active {

  }

  .disabled {
    text-decoration : line-through;
  }
</style>


<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Chat Room</h3>
  </div>
  <div class="panel-body">

    <?php foreach ((array) $Chat->ChatRoom->getRoomMessages((!empty($_SESSION['user_data']['room_id'])) ? $_SESSION['user_data']['room_id'] : 1) as $MessageItem): ?>
      <div class="<?php print $MessageItem['status']; ?>">
        <?php if ($MessageItem['user_id'] != $_SESSION['user_data']['user_id']): ?>
          <span class="btn btn-default send-private-message" data-id="<?php print $MessageItem['user_id']; ?>" data-name="<?php print $MessageItem['username']; ?>">Reply</span>
        <?php endif; ?>
        <strong><?php print $MessageItem['username']; ?>:</strong>
        <span><?php print $MessageItem['message'] ?></span>
        <?php if ($Chat->ChatRoom->ActionAccess->access('can remove messages')): ?>
          <span class="btn btn-default remove-message" data-id="<?php print $MessageItem['message_id'] ?>">Remove</span>
        <?php endif; ?>

      </div>
    <?php endforeach; ?>

  </div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">My private messages</h3>
  </div>
  <div class="panel-body">
    <?php foreach ((array) $Chat->ChatRoom->getPrivateMessages($_SESSION['user_data']['user_id']) as $MessageItem): ?>
      <div class="<?php print $MessageItem['status']; ?>">
        <?php if ($MessageItem['user_id'] != $_SESSION['user_data']['user_id']): ?>
          <span class="btn btn-default send-private-message" data-id="<?php print $MessageItem['user_id']; ?>" data-name="<?php print $MessageItem['username']; ?>">Reply</span>
        <?php endif; ?>
        <strong><?php print $MessageItem['username']; ?></strong> to :
        <strong><?php print $User->getUserNameByID($MessageItem['recipient_id']); ?></strong>
        <span><?php print $MessageItem['message'] ?></span>
        <?php if ($Chat->ChatRoom->ActionAccess->access('can remove messages')): ?>
          <span class="btn btn-default remove-message" data-id="<?php print $MessageItem['message_id'] ?>">Remove</span>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php if ($Chat->ChatRoom->ActionAccess->access('can administer private messages')): ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Administer private messages</h3>
    </div>
    <div class="panel-body">
      <?php foreach ((array) $Chat->ChatRoom->getAllPrivateMessages() as $MessageItem): ?>
        <div class="<?php print $MessageItem['status']; ?>">
          <?php if ($MessageItem['user_id'] != $_SESSION['user_data']['user_id']): ?>
            <span class="btn btn-default send-private-message" data-id="<?php print $MessageItem['user_id']; ?>" data-name="<?php print $MessageItem['username']; ?>">Reply</span>
          <?php endif; ?>
          <strong><?php print $MessageItem['username']; ?></strong> to :
          <strong><?php print $User->getUserNameByID($MessageItem['recipient_id']); ?></strong>
          <span><?php print $MessageItem['message'] ?></span>
          <?php if ($Chat->ChatRoom->ActionAccess->access('can remove messages')): ?>
            <span class="btn btn-default remove-message" data-id="<?php print $MessageItem['message_id'] ?>">Remove</span>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>
