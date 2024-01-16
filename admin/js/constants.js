const ADMIN_URL = "http://localhost/project/admin";
const API_URL = "http://localhost/project/api";

function api(path)
{
    return `${API_URL}/${path}`;
}

function admin(path)
{
    return `${ADMIN_URL}/${path}`;
}

function uploads(path)
{
    return api(`public/uploads/${path}`);
}