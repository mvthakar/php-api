<?php Authorize::forRoles(['Admin']);?>
<style>
  .table th
  {
    border-top: none;
  }
</style>
<div class="container">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Order Details</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=urlOf('index.php')?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?=urlOf('orders')?>">Orders</a></li>
            <li class="breadcrumb-item active">Details</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content mx-5">
    <div class="row">&nbsp;</div>
    <div class="row">&nbsp;</div>
    <div class="card card-outline card-info">
      <div class="card-body">
        <div class="col">
          <div class="row">
            <div class="col">
              <div class="card card-outline card-info m-3">
                <div class="card-header h5">
                  Order information
                </div>
                <div class="card-body">
                  <div class="row">
                    <table class="col mx-2 info" id="order-info">
                      <tr>
                        <td class="py-5 text-center" colspan="2">
                          <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="card card-outline card-info m-3">
                <div class="card-header h5">
                  Customer information
                </div>
                <div class="card-body">
                  <div class="row">
                    <table class="col mx-2 info" id="customer-info">
                      <tr>
                        <td class="py-5 text-center" colspan="2">
                          <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                          </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="card card-outline card-info m-3">
                <div class="card-header h5">
                  Ordered Products
                </div>
                <div class="card-body" style="padding-top: 0;">
                  <div class="row">
                    <table id="items" class="table table-striped item-list">
                      <thead>
                        <tr>
                          <th width="20%" scope="col">Image</th>
                          <th width="30%" scope="col">Name</th>
                          <th width="20%" scope="col">Ind. Price</th>
                          <th width="10%" scope="col">Quantity</th>
                          <th width="20%" scope="col">Price</th>
                        </tr>
                      </thead>
                      <tbody id="ordered-products">
                        <tr>
                          <td class="py-5 text-center" colspan="5">
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
            </div>
          </div>
        </div>
      </div>
    </div>
    <br>
  </section>
</div>
<script src="<?=urlOf('js/order-details.js')?>"></script>