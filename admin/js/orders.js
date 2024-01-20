function statusChanged()
{
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('status', $('#status').val());

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

function createPageButtons(pageCount, currentPage, itemsPerPage)
{
    if (!currentPage)
        currentPage = 1;

    let prevButton = `
        <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="${currentPage > 1 ? admin(`orders/?page=${parseInt(currentPage) - 1}&count=${itemsPerPage}`) : '#'}" tabindex="-1">
                <i class="fas fa-arrow-left"></i>
            </a>
        </li>
    `;

    let nextButton = `
        <li class="page-item ${currentPage >= pageCount ? 'disabled' : ''}">
            <a class="page-link" href="${currentPage < pageCount ? admin(`orders/?page=${parseInt(currentPage) + 1}&count=${itemsPerPage}`) : '#'}" tabindex="-1">
                <i class="fas fa-arrow-right"></i>
            </a>
        </li>
    `;

    let numberedButtons = ``;
    for (let i = 1; i <= pageCount; i++)
    {
        numberedButtons += `
            <li class="page-item ${currentPage == i ? 'active' : ''}">
                <a class="page-link" href="${admin(`orders/?page=${i}&count=${itemsPerPage}`)}">${i}</a>
            </li>
        `;
    }

    let buttons = `${prevButton}${numberedButtons}${nextButton}`;
    $('#page-buttons').html(buttons);
}

async function loadOrders()
{
    const status = $("#status").val();

    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') ?? 1;
    const itemsPerPage = urlParams.get('count') ?? 10;

    let formData = new FormData();
    formData.append('page', currentPage);
    formData.append('count', itemsPerPage);
    
    if (status != "All")
        formData.append('status', status);

    let query = new URLSearchParams(formData).toString();
    let ordersResponse = await get(`orders/admin/list.php?${query}`);

    console.log(ordersResponse);

    let rows = "";
    ordersResponse.message.orders.forEach((order, index) => {
        rows += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>${order.name}</td>
                <td>${order.mobileNumber}</td>
                <td>${order.totalPriceWithTax}</td>
                <td>${order.orderStatus}</td>
                <td>
                    <a role="button" class="btn btn-primary" href="${admin(`orders/admin/view.php?id=${order.slug}`)}" data-toggle="tooltip" data-placement="bottom" title="View">
                        <i class="far fa-eye"></i>
                    </a>
                </td>
            </tr>
            `;
    });

    $('#orders').html(rows);

    createPageButtons(ordersResponse.message.pageCount, currentPage, itemsPerPage);
}

$(loadOrders);