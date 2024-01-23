<div class="container">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Edit Category</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=urlOf('index.php')?>">Home</a></li>
            <li class="breadcrumb-item active"><a href="<?=urlOf('categories')?>">Categories</a></li>
            <li class="breadcrumb-item active">Edit</li>
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
          <form onsubmit="editCategory(event);">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="category-name">Category Name</label>
                  <input type="text" class="form-control" id="category-name" placeholder="Enter name" autofocus
                    required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <label for="images">Category Images</label>
                <div class="input-group">
                  <div class="custom-file">
                    <input accept="image/png, image/jpeg" type="file" class="custom-file-input" id="category-image" onchange="categoryImageSelected();">
                    <label class="custom-file-label" for="category-image">Select image</label>
                  </div>
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger" onclick="clearCategoryImage()">Clear</button>
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
                <span class="btn-submit-text-saved" style="display: none">Saved!</span>
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

<script src="<?=urlOf('js/categories.js')?>"></script>
<script>
  async function populate()
  {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    let response = await get(`categories/get-by-id.php?id=${id}`);
    if (response.status != 200)
      window.location.href = admin('categories');

    let image = uploads(`categories/${response.message.imageFileName ?? 'default.jpg'}`);

    $('#category-name').val(response.message.name);
    $('#uploaded-image-preview img').attr('src', image);
    $('#uploaded-image-preview img').attr('data-default-src', image);
  }

  $(populate);
</script>