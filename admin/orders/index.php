<?php 

Authorize::forRoles(["Admin"]);
$count = $_GET['count'] ?? 10;
$status = $_GET['status'] ?? "All";

?>

<div class="container">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Orders</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=urlOf('index.php')?>">Home</a></li>
            <li class="breadcrumb-item active">Orders</li>
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
            <b>Status:</b>
          </div>
          <div class="col">
            <select name="status" id="status" class="custom-select" onchange="statusChanged()">
              <option <?= $status == "All" ? "selected" : "" ?> value="">All</option>
              <option <?= $status == "Placed" ? "selected" : "" ?> value="Placed">Placed</option>
              <option <?= $status == "On the way" ? "selected" : "" ?> value="On the way">On the way</option>
              <option <?= $status == "Delivered" ? "selected" : "" ?> value="Delivered">Delivered</option>
              <option <?= $status == "Canceled" ? "selected" : "" ?> value="Canceled">Canceled</option>
              <option <?= $status == "Rejected" ? "selected" : "" ?> value="Rejected">Rejected</option>
            </select>
          </div>
        </div>
      </div>
      <div class="col col-4 offset-4" style="text-align: end">
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
                <th width="20%" scope="col">Customer name</th>
                <th width="20%" scope="col">Mobile number</th>
                <th width="20%" scope="col">Total Price (inc. Tax)</th>
                <th width="20%" scope="col">Status</th>
                <th width="10%" scope="col">View</th>
              </tr>
            </thead>
            <tbody id="orders">
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
<script src="<?=urlOf('js/orders.js')?>"></script>