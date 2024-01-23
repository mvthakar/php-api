async function updateStatus(select, orderId)
{
    const status = select.value;
    const elementId = $(select).data('id');

    $(`#status-working-${elementId}`).removeClass("d-none");
    await post('orders/admin/change-status.php', { orderId: orderId, status: status });
    
    $(`#status-working-${elementId}`).addClass("d-none");
    $(`#status-success-${elementId}`).removeClass("d-none");
    $(`#order-status-${elementId}`).html(status);

    setTimeout(() => $(`#status-success-${elementId}`).addClass("d-none"), 3000);
}

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

    let rows = "";
    if (ordersResponse.message == null || ordersResponse.message.orders == null)
        return;

    ordersResponse.message.orders.forEach((order, index) => {
        rows += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>${order.orderedOnDateTime}</td>
                <td>${order.name}</td>
                <td>${order.mobileNumber}</td>
                <td>${order.totalPriceWithTax}</td>
                <td id="order-status-${index}">${order.orderStatus}</td>
                <td>
                    <a role="button" class="btn btn-primary" href="${admin(`orders/view.php?id=${order.slug}`)}" data-toggle="tooltip" data-placement="bottom" title="View">
                        <i class="far fa-eye"></i>
                    </a>
                </td>
                <td>
                    <div class="row">
                        <div class="col">
                            <select data-id="${index}" ${order.orderStatus == "Canceled" ? "disabled" : ""} class="custom-select" onchange="updateStatus(this, '${order.slug}')">
                                <option value="On the way">On the way</option>
                                <option value="Delivered">Delivered</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col col-2 d-flex justify-content-center align-items-center">
                            <div id="status-working-${index}" class="spinner-border spinner-border-sm d-none" role="status">
                                <span class="sr-only">Chakedi</span>
                            </div>
                            <div id="status-success-${index}" class="d-none">
                                <i class="far fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            `;
    });

    $('#orders').html(rows);

    createPageButtons(ordersResponse.message.pageCount, currentPage, itemsPerPage);
}

$(loadOrders);