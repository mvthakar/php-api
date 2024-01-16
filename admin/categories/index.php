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
                  <th width="10%" scope="col" class="text-center">Number</th>
                  <th width="10%" scope="col" class="text-center d-sm-none d-md-table-cell d-none d-sm-table-cell">Image</th>
                  <th width="" scope="col">Name</th>
                  <th width="20%" scope="col">Actions</th>
                </tr>
              </thead>
              <tbody id="categories">
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
<div class="position-fixed bottom-0 right-0 p-3" style="z-index: 5; right: 0; bottom: 0;">
  <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-header bg-danger">
      <strong class="mr-auto">Error</strong>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body bg-danger" id="error-message">
      
    </div>
  </div>
</div>

<script src="<?=urlOf('js/categories.js')?>"></script>