function categoryChanged()
{
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('category', $('#categories').val());

    const query = urlParams.toString();
    window.location.href = `./?${query}`;
}

function changeNumberOfRows()
{
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('count', $('#count').val());

    const query = urlParams.toString();
    window.location.href = `./?${query}`;
}

function productImagesSelected()
{
    let files = $('#product-images')[0].files;
        let html = '';

    for (let i = 0; i < files.length; i++) {
        let image = URL.createObjectURL(files[i]);
        html += `<img src="${image}" class="preview-item" />`;
    }

    $('.custom-file-label').text(`${files.length} files selected.`);
    $('#uploaded-image-preview').html(html);
}

function clearProductImages()
{
    $('#product-images').val('');
    $('.custom-file-label').text(`Select images`);

    $('#uploaded-image-preview').html('<img src="../assets/images/borders.png" alt="preview photo" class="preview-item" />');
}

async function deleteProductImage(id)
{
    if ($('.img-wrap').length == 1)
    {
        alert('Product must have at least 1 image');
        return;
    }

    $('#delete-' + id).css('display', 'none');
    $('#delete-spinner-' + id).css('display', 'unset');
    $('#img-preview-' + id).addClass('deleting');

    let response = await get(`products/delete-image.php?id=${id}`);
    if (response.status != 200)
    {
        alert('Cannot delete this image');
        return;
    }

    $('#img-wrap-' + id).remove();
}

async function createProduct(event)
{
    event.preventDefault();

    let files = $('#product-images')[0].files;
    let formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('images[]', files[i]);
    }

    if (files.length == 0)
    {
        alert('Product must have at least one image');
        return;
    }

    $('#btn-submit').attr('disabled', '');
    $('.btn-submit-text').hide();
    $('.btn-submit-text-saved').hide();
    $('.btn-submit-spinner').show();

    let name = $('#product-name').val();
    let price = $('#product-price').val();
    let description = $('#product-description').val();
    let categories = $('#categories').val();
    let response = await post('products/add.php', {
        name: name,
        price: price,
        description: description,
        categories: categories
    });

    let productId = response.message;
    let imageResponse = await fetch(
        api(`products/images.php?id=${productId}`),
        {
            method: 'POST',
            body: formData,
            headers: {
                contentType: false,
                processData: false
            }
        }
    );

    if (imageResponse.status != 200) 
    {
        alert('uh huh');
        return;
    }

    $('.btn-submit-text').hide();
    $('.btn-submit-text-saved').show();
    $('.btn-submit-spinner').hide();

    setTimeout(() => window.location.href = admin('products'), 1000);
}

async function editProduct(event)
{
    event.preventDefault();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    let files = $('#product-images')[0].files;
    let formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('images[]', files[i]);
    }

    $('#btn-submit').attr('disabled', '');
    $('.btn-submit-text').hide();
    $('.btn-submit-text-saved').hide();
    $('.btn-submit-spinner').show();

    let name = $('#product-name').val();
    let price = $('#product-price').val();
    let description = $('#product-description').val();
    let categories = $('#categories').val();
    let response = await post(`products/update.php?id=${id}`, {
        name: name,
        price: price,
        description: description,
        categories: categories
    });

    let imageResponse = await fetch(
        api(`products/images.php?id=${id}`),
        {
            method: 'POST',
            body: formData,
            headers: {
                contentType: false,
                processData: false
            }
        }
    );

    if (imageResponse.status != 200) 
    {
        alert('uh huh');
        return;
    }

    $('.btn-submit-text').hide();
    $('.btn-submit-text-saved').show();
    $('.btn-submit-spinner').hide();

    setTimeout(() => window.location.href = admin('products'), 1000);
}

function showDeleteConfirmation(id)
{
    $('#btn-yes').attr('data-id', id);
    $('#modal-delete').modal('show');
}

async function deleteItem()
{
    let id = $('#btn-yes').attr('data-id');
    if (id == null)
        return;

    let response = await get(`products/delete.php?id=${id}`);
    if (response.status == 200)
    {
        window.location.reload();
        return;
    }

    $('#error-message').text(response.message);
    $('.modal').modal('hide');
    $('.toast').toast('show');
}

async function loadCategories()
{
    const urlParams = new URLSearchParams(window.location.search);
    const selectedCategory = urlParams.get('category');

    let response = await get('categories/all.php');
    let options = '';
    
    response.message.categories.forEach(category => 
    {
        options += `<option ${category.slug == selectedCategory ? 'selected' : '' } value="${category.slug}">${category.name}</option>`;
    });

    $('#categories').html(options);
}

async function loadProducts()
{
    const categoryId = $('#categories').val();

    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') ?? 1;
    const itemsPerPage = urlParams.get('count') ?? 10;
    const search = urlParams.get('search') ?? null;

    let formData = new FormData();
    formData.append('id', categoryId);
    formData.append('page', currentPage);
    formData.append('count', itemsPerPage);
    formData.append('includeOutOfStock', 1);
    if (search != null)
        formData.append('search', search);

    let query = new URLSearchParams(formData).toString();
    let productsResponse = await get(`products/for-category.php?${query}`);

    let rows = "";
    productsResponse.message.products.forEach((category, index) => {
        rows += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td class="text-center d-sm-none d-sm-none d-md-table-cell d-none d-sm-table-cell"><img width="50px" src="${uploads(`products/${category.imageFileName ?? 'default.jpg'}`)}" /></td>
                <td>${category.name}</td>
                <td>
                    <div class="btn-group d-none d-lg-flex" role="group" aria-label="Actions">
                        <a role="button" class="btn btn-primary" href="${admin(`products/edit.php?id=${category.slug}`)}" data-toggle="tooltip" data-placement="bottom" title="Edit">
                            <i class="far fa-edit"></i>
                        </a>
                        <a role="button" class="btn btn-danger" onclick="showDeleteConfirmation('${category.slug}')" data-toggle="tooltip" data-placement="bottom" title="Delete">
                            <i class="far fa-trash-alt"></i>
                        </a>
                    </div>
                    <div class="btn-group-vertical d-lg-none" role="group" aria-label="Actions">
                        <a role="button" class="btn btn-primary" href="${admin(`products/edit.php?id=${category.slug}`)}"
                        data-toggle="tooltip" data-placement="bottom" title="Edit">
                        <i class="far fa-edit"></i>
                        </a>
                        <a role="button" class="btn btn-danger"
                        onclick="showDeleteConfirmation('${category.slug}')" data-toggle="tooltip"
                        data-placement="bottom" title="Delete">
                        <i class="far fa-trash-alt"></i>
                        </a>
                    </div>
                </td>
            </tr>
            `;
    });

    $('#products').html(rows);

    await createPageButtons(productsResponse.message.pageCount, currentPage, itemsPerPage, search);
}

async function createPageButtons(pageCount, currentPage, itemsPerPage, search)
{
    if (!currentPage)
        currentPage = 1;

    const searchQuery = search != null ? `&search=${search}` : '';

    let prevButton = `
        <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="${currentPage > 1 ? admin(`products/?page=${parseInt(currentPage) - 1}&count=${itemsPerPage}${searchQuery}`) : '#'}" tabindex="-1">
                <i class="fas fa-arrow-left"></i>
            </a>
        </li>
    `;

    let nextButton = `
        <li class="page-item ${currentPage >= pageCount ? 'disabled' : ''}">
            <a class="page-link" href="${currentPage < pageCount ? admin(`products/?page=${parseInt(currentPage) + 1}&count=${itemsPerPage}${searchQuery}`) : '#'}" tabindex="-1">
                <i class="fas fa-arrow-right"></i>
            </a>
        </li>
    `;

    let numberedButtons = ``;
    for (let i = 1; i <= pageCount; i++)
    {
        numberedButtons += `
            <li class="page-item ${currentPage == i ? 'active' : ''}">
                <a class="page-link" href="${admin(`products/?page=${i}&count=${itemsPerPage}${searchQuery}`)}">${i}</a>
            </li>
        `;
    }

    let buttons = `${prevButton}${numberedButtons}${nextButton}`;
    $('#page-buttons').html(buttons);
}

async function init()
{
    await loadCategories();
    await loadProducts();
}

$(init);