<div class="panel panel-default">
  <div class="panel-body">

    <form action="/login" method="post">
      <?php print genCSRFProtection(); ?>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" name="username" placeholder="Enter username" id="username" />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" />
      </div>

      <button type="submit" class="btn btn-default">Login</button>

      <a class="btn btn-link" href="/register">Register</a>

    </form>

  </div>
</div>
