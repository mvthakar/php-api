<?php $count = $_GET['count'] ?? 10; ?>
<div class="container">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Products</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=urlOf('index.php')?>">Home</a></li>
            <li class="breadcrumb-item active">Products</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content mx-5">
    <form class="row" method="get">
      <div class="col col-4">
        <div class="row">
          <div class="col col-3 d-flex align-items-center">
            <b>Category:</b>
          </div>
          <div class="col">
            <select name="category" id="categories" class="custom-select" onchange="categoryChanged()">
              
            </select>
          </div>
        </div>
      </div>
      <div class="col col-4">
        <div class="input-group">
          <input value="<?= $_GET['search'] ?? '' ?>" type="text" class="form-control" name="search" placeholder="Search" autofocus>
          <div class="input-group-append">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="col col-4" style="text-align: end">
        <div class="row">
          <div class="col d-flex justify-content-end align-items-center">
            <b>Rows:</b>
          </div>
          <div class="col">
            <select name="count" id="count" class="custom-select" onchange="changeNumberOfRows()">
              <option <?= $count == "10" ? "selected" : "" ?> value="10">10</option>
              <option <?= $count == "20" ? "selected" : "" ?> value="20">20</option>
              <option <?= $count == "30" ? "selected" : "" ?> value="30">30</option>
            </select>
          </div>
          <div class="col">
            <a role="button" class="btn btn-success" href="<?=urlOf('products/create.php')?>">Create</a>
          </div>
        </div>
      </div>
    </form>
    <div class="row">&nbsp;</div>
    <div class="card card-outline card-info">
      <div class="card-body">
        <div class="col col-md-12">
          <table id="items" class="table table-striped item-list">
            <thead>
              <tr>
                <th width="10%" scope="col" class="text-center">Number</th>
                <th width="10%" scope="col" class="text-center d-sm-none d-md-table-cell d-none d-sm-table-cell">Image
                </th>
                <th width="" scope="col">Name</th>
                <th width="20%" scope="col">Actions</th>
              </tr>
            </thead>
            <tbody id="products">
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
      <div class="card-footer d-flex justify-content-end">
        <nav aria-label="page-buttons" class="mt-3">
          <ul class="pagination" id="page-buttons">

          </ul>
        </nav>
      </div>
    </div>
    <br>
  </section>
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
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-labelledby="modal-delete-title"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-delete-title">Cofirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this item?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        <button id="btn-yes" type="button" class="btn btn-danger" onclick="deleteItem()">Yes</button>
      </div>
    </div>
  </div>
</div>
<script src="<?=urlOf('js/products.js')?>"></script>