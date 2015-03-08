<div class="panel panel-default">
  <div class="panel-body">

    <form action="/register" method="post">
      <?php print genCSRFProtection(); ?>

      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" name="username" placeholder="Enter username" id="username" />

      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" />
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Enter confirm password" />
      </div>

      <button type="submit" class="btn btn-default">Sign In</button>

      <a class="btn btn-link" href="/login">Login</a>
    </form>


  </div>
</div>
