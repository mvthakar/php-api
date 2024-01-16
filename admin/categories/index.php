<?php Authorize::forRoles(["Admin"]);?>
<div class="content-wrapper">
  <div class="container">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Categories</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?=urlOf('index.php')?>">Home</a></li>
              <li class="breadcrumb-item active">Manage Categories</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
    <section class="content mx-5">
      <div class="row">
        <div class="col" style="text-align: end">
          <a role="button" class="btn btn-success" href="<?=urlOf('categories/create.php')?>">Create New</a>
        </div>
      </div>
      <div class="row">&nbsp;</div>
      <div class="card card-outline card-info">
        <div class="card-body">
          <div class="col col-md-12">
            <table id="items" class="table table-striped item-list">
              <thead>
                <tr>
                  <th scope="col">Number</th>
                  <th scope="col">Image</th>
                  <th scope="col">Name</th>
                  <th scope="col">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="py-5 text-center" colspan="4">
                    <div class="spinner-border" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <br>
    </section>
  </div>
</div>
<script src="<?=urlOf('js/categories.js')?>"></script>