async function loadOrderDetails()
{
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('id') ?? null;

    if (orderId == null)
        window.location.href = admin('orders');

    let response = await get(`orders/admin/view.php?id=${orderId}`);
    if (response.status == 404)
        window.location.href = admin('orders');

    populateOrderInfo(response.message);
    populateCustomerInfo(response.message);
    populateOrderedProducts(response.message.products);
}

function populateOrderInfo(data)
{
    let html = `
        <tr>
            <td><b>Ordered on</b></td>
            <td>${data.orderedOnDateTime}</td>
        </tr>
        <tr>
            <td><b>Delivered on</b></td>
            <td>${data.deliveredOnDateTime ?? '-'}</td>
        </tr>
        <tr>
            <td><b>Price excl. Tax</b></td>
            <td>${data.totalPriceWithoutTax}</td>
        </tr>
        <tr>
            <td><b>CGST ₹</b></td>
            <td>${data.cgstAmount} (${data.cgstPercentage}%)</td>
        </tr>
        <tr>
            <td><b>SGST ₹</b></td>
            <td>${data.sgstAmount} (${data.sgstPercentage}%)</td>
        </tr>
        <tr>
            <td><b>Total ₹</b></td>
            <td>${data.totalPriceWithTax}</td>
        </tr>
        <tr>
            <td><b>Status</b></td>
            <td>${data.orderStatus}</td>
        </tr>
    `;

    $('#order-info').html(html);
}

function populateCustomerInfo(data)
{
    let html = `
        <tr>
            <td><b>Email</b></td>
            <td>${data.userEmail}</td>
        </tr>
        <tr>
            <td><b>Name</b></td>
            <td>${data.name}</td>
        </tr>
        <tr>
            <td><b>Mobile Number</b></td>
            <td>${data.mobileNumber}</td>
        </tr>
        <tr>
            <td><b>Address</b></td>
            <td>${data.address}</td>
        </tr>
        <tr>
            <td><b>Pincode</b></td>
            <td>${data.pincode}</td>
        </tr>
        <tr>
            <td><b>City</b></td>
            <td>${data.city}</td>
        </tr>
        <tr>
            <td><b>State</b></td>
            <td>${data.state}</td>
        </tr>
    `;

    $('#customer-info').html(html);
}

function populateOrderedProducts(products)
{
    let html = "";
    for (let i = 0; i < products.length; i++)
    {
        html += `
            <tr>
                <td><img width="50px" src="${uploads(`products/${products[i].productImage}`)}" alt="product image"></td>
                <td>${products[i].name}</td>
                <td>${products[i].individualPrice}</td>
                <td>${products[i].quantity}</td>
                <td>${products[i].price}</td>
            </tr>
        `;
    }

    $('#ordered-products').html(html);
}

$(loadOrderDetails);