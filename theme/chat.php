<div class="container-fluid">
  <div class="row">
    <div class="panel panel-default">
      <div class="panel-body">
        <a href="/logout"><strong><?php print $_SESSION['user_data']['username']; ?></strong> LogOut</a>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-8">
      <div class="panel panel-default">
        <div class="panel-body">

          <div id="room-area"></div>
          <div>
            <div class="form-inline">
              <div class="form-group">
                <label class="sr-only" for="exampleInputAmount">Write private message</label>

                <div class="input-group">
                  <div class="input-group-addon"><span>to</span></div>
                  <input name="to-recipient-id" class="hidden" type="text" class="form-control" id="exampleInputAmount" placeholder="Click to 'private' button near username">
                  <input name="to-recipient-name" type="text" class="form-control" id="exampleInputAmount" placeholder="Click to 'private' button near username">
                </div>
              </div>
            </div>
            <textarea class="form-control" rows="3"></textarea>
            <button id="send-data" type="submit" class="btn btn-primary">Send</button>
          </div>


        </div>
      </div>

    </div>
    <div class="col-md-4">

      <div class="panel panel-default">
        <div class="panel-body">
          <div id="room-users"></div>

          <div class="input-group" id="new-room-action">
            <input type="text" name="new-room" placeholder="New Room" class="form-control" />
            <span class="input-group-btn">
              <button class="btn btn-default" type="button">Go!</button>
            </span>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

