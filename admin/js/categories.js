function changeNumberOfRows()
{
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('count', $('#count').val());

    const query = urlParams.toString();
    window.location.href = `./?${query}`;
}

function categoryImageSelected()
{
    let file = $('#category-image')[0].files[0];
    if (file == null)
        return;

    let image = URL.createObjectURL(file);
    
    $('.custom-file-label').text(`${file.name} files selected.`);
    $('#uploaded-image-preview img').attr('src', image);
}

function clearCategoryImage()
{
    $('#category-image').val('');
    $('.custom-file-label').text(`Select image`);

    let defautSrc = $('#uploaded-image-preview img').attr('data-default-src');
    $('#uploaded-image-preview img').attr('src', defautSrc ?? '../assets/images/borders.png');
}

async function createCategory(event)
{
    event.preventDefault();

    $('#btn-submit').attr('disabled', '');
    $('.btn-submit-text').hide();
    $('.btn-submit-text-saved').hide();
    $('.btn-submit-spinner').show();

    let name = $('#category-name').val();
    let response = await post('categories/add.php', { name: name });
    
    let cateogryId = response.message;
    let image = $('#category-image')[0].files[0];

    if (image != null)
    {
        let formData = new FormData();
        formData.append("image", image);
    
        let imageResponse = await fetch(
            api(`categories/image.php?id=${cateogryId}`),
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
    }

    $('.btn-submit-text').hide();
    $('.btn-submit-text-saved').show();
    $('.btn-submit-spinner').hide();

    setTimeout(() => window.location.href = admin('categories'), 1000);
}

async function editCategory(event)
{
    event.preventDefault();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    $('#btn-submit').attr('disabled', '');
    $('.btn-submit-text').hide();
    $('.btn-submit-text-saved').hide();
    $('.btn-submit-spinner').show();

    let name = $('#category-name').val();
    let response = await post(`categories/update.php?id=${id}`, { name: name });
    
    if (response.status != 200)
    {
        alert('Uh huh');
        return;
    }

    let image = $('#category-image')[0].files[0];

    if (image != null)
    {
        let formData = new FormData();
        formData.append("image", image);
    
        let imageResponse = await fetch(
            api(`categories/image.php?id=${id}`),
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
    }

    $('.btn-submit-text').hide();
    $('.btn-submit-text-saved').show();
    $('.btn-submit-spinner').hide();

    setTimeout(() => window.location.href = admin('categories'), 1000);
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

    let response = await get(`categories/delete.php?id=${id}`);
    if (response.status == 200)
    {
        window.location.reload();
        return;
    }

    $('#error-message').text(response.message);
    $('.modal').modal('hide');
    $('.toast').toast('show');
}

async function loadItems()
{
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') ?? 1;
    const itemsPerPage = urlParams.get('count') ?? 10;
    const search = urlParams.get('search') ?? null;

    let formData = new FormData();
    formData.append('page', currentPage);
    formData.append('count', itemsPerPage);
    if (search != null)
        formData.append('search', search);

    let query = new URLSearchParams(formData).toString();
    let categoriesResponse = await get(`categories/get.php?${query}`);

    let rows = "";
    categoriesResponse.message.categories.forEach((category, index) => {
        rows += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td class="text-center d-sm-none d-sm-none d-md-table-cell d-none d-sm-table-cell"><img width="50px" src="${uploads(`categories/${category.imageFileName ?? 'default.jpg'}`)}" /></td>
                <td>${category.name}</td>
                <td>
                    <div class="btn-group d-none d-lg-flex" role="group" aria-label="Actions">
                        <a role="button" class="btn btn-primary" href="${admin(`categories/edit.php?id=${category.slug}`)}" data-toggle="tooltip" data-placement="bottom" title="Edit">
                            <i class="far fa-edit"></i>
                        </a>
                        <a role="button" class="btn btn-danger" onclick="showDeleteConfirmation('${category.slug}')" data-toggle="tooltip" data-placement="bottom" title="Delete">
                            <i class="far fa-trash-alt"></i>
                        </a>
                    </div>
                    <div class="btn-group-vertical d-lg-none" role="group" aria-label="Actions">
                        <a role="button" class="btn btn-primary" href="${admin(`categories/edit.php?id=${category.slug}`)}"
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

    $('#categories').html(rows);

    await createPageButtons(categoriesResponse.message.pageCount, currentPage, itemsPerPage, search);
}

async function createPageButtons(pageCount, currentPage, itemsPerPage, search)
{
    if (!currentPage)
        currentPage = 1;

    const searchQuery = search != null ? `&search=${search}` : '';

    let prevButton = `
        <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="${currentPage > 1 ? admin(`categories/?page=${parseInt(currentPage) - 1}&count=${itemsPerPage}${searchQuery}`) : '#'}" tabindex="-1">
                <i class="fas fa-arrow-left"></i>
            </a>
        </li>
    `;

    let nextButton = `
        <li class="page-item ${currentPage >= pageCount ? 'disabled' : ''}">
            <a class="page-link" href="${currentPage < pageCount ? admin(`categories/?page=${parseInt(currentPage) + 1}&count=${itemsPerPage}${searchQuery}`) : '#'}" tabindex="-1">
                <i class="fas fa-arrow-right"></i>
            </a>
        </li>
    `;

    let numberedButtons = ``;
    for (let i = 1; i <= pageCount; i++)
    {
        numberedButtons += `
            <li class="page-item ${currentPage == i ? 'active' : ''}">
                <a class="page-link" href="${admin(`categories/?page=${i}&count=${itemsPerPage}${searchQuery}`)}">${i}</a>
            </li>
        `;
    }

    let buttons = `${prevButton}${numberedButtons}${nextButton}`;
    $('#page-buttons').html(buttons);
}

$(loadItems);