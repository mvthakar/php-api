<?php Authorize::forRoles(); ?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"></h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item">Settings</li>
          <li class="breadcrumb-item active">Change password</li>
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
            <h3 class="card-title">Change Password</h3>
          </div>
          <form onsubmit="changePassword(event)">
            <div class="card-body">
              <div class="form-group">
                <label for="password">Old Password</label>
                <input type="password" name="old-password" id="old-password" class="form-control"
                  placeholder="Old Password" autofocus required>
              </div>
              <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="new-password" id="new-password" class="form-control"
                  placeholder="New Password" required>
              </div>
              <div class="form-group">
                <label for="password">Confirm New Password</label>
                <input type="password" name="confirm-password" id="confirm-password" class="form-control"
                  placeholder="Confirm New Password" required>
              </div>
            </div>
            <div class="card-footer">
              <button id="btn-submit" type="submit" class="btn btn-primary">
                <span id="btn-submit-text">Save</span>
                <span id="btn-submit-text-saved" style="display: none">Saved!</span>
                <div id="btn-submit-spinner" class="spinner-border spinner-border-sm" role="status"
                  style="display: none">
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
