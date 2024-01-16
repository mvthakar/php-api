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

    await get(`categories/delete.php?id=${id}`);
}

async function loadItems()
{
    let response = await get('categories/get.php');
    // ...
}

$(loadItems);