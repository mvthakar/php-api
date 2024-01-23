<div class="container">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Create Product</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=urlOf('index.php')?>">Home</a></li>
            <li class="breadcrumb-item active"><a href="<?=urlOf('products')?>">Products</a></li>
            <li class="breadcrumb-item active">Create</li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content mx-5">
    <div class="row">&nbsp;</div>
    <div class="card card-outline card-info">
      <div class="card-body">
        <div class="col col-md-12">
          <form onsubmit="createProduct(event);">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="category">Categories (Hold control while selecting or deselecting)</label>
                  <select id="categories" class="custom-select" multiple required>
                    
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="product-name">Product Name</label>
                  <input type="text" class="form-control" id="product-name" placeholder="Enter name" autofocus required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="product-price">Product Price</label>
                  <input type="text" class="form-control" id="product-price" placeholder="Enter price" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="product-description">Product Description</label>
                  <textarea class="form-control" rows="5" id="product-description" placeholder="Enter description"></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <label for="images">Product Images</label>
                <div class="input-group">
                  <div class="custom-file">
                    <input multiple accept="image/png, image/jpeg" type="file" class="custom-file-input" id="product-images" onchange="productImagesSelected();">
                    <label class="custom-file-label" for="product-images">Select images</label>
                  </div>
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger" onclick="clearProductImages()">Clear</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col">
                <div><b><p>Preview</p></b></div>
                <div class="input-group">
                  <div class="container">
                    <div id="uploaded-image-preview" class="row">
                      <img src="<?= urlOf('assets/images/borders.png') ?>" alt="preview photo" class="preview-item" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer mt-3">
              <button id="btn-submit" type="submit" class="btn btn-success btn-submit">
                <span class="btn-submit-text">Submit</span>
                <span class="btn-submit-text-saved" style="display: none">Created!</span>
                <div class="btn-submit-spinner spinner-border spinner-border-sm" role="status" style="display: none">
                  <span class="sr-only">Loading...</span>
                </div>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <br>
</div>

<script src="<?=urlOf('js/products.js')?>"></script>