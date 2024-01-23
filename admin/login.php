<?php Authorize::onlyAnonymous(); ?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"></h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Login</li>
        </ol>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-3"></div>
      <div class="col-md-6">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Admin Panel Login</h3>
          </div>
          <form onsubmit="login(event)">
            <div class="card-body">
              <div class="form-group">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" class="form-control" placeholder="Enter email" autofocus
                  required>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password"
                  required>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <button id="btn-submit" type="submit" class="btn btn-primary btn-login">
                <span id="btn-submit-text">Submit</span>
                <span id="btn-submit-text-saved" style="display: none">Logged in!</span>
                <div id="btn-submit-spinner" class="spinner-border spinner-border-sm" role="status" style="display: none">
                  <span class="sr-only">Loading...</span>
                </div>
              </button>
            </div>
          </form>
        </div>
        <div id="message">
        </div>
      </div>
      <div class="col-md-3"></div>
    </div>
  </div>
</section>
