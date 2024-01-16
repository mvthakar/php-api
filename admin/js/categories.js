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
    let response = await get('categories/get.php');
    let rows = "";

    response.message.forEach((category, index) => {
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
                    <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog"
                        aria-labelledby="modal-delete-title" aria-hidden="true">
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
                            <button id="btn-yes" type="button" class="btn btn-danger"
                                onclick="deleteItem()">Yes</button>
                            </div>
                        </div>
                        </div>
                    </div>
                </td>
            </tr>
            `;
    });

    $('#categories').html(rows);
}

$(loadItems);