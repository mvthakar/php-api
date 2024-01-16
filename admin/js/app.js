class UnauthorizedError extends Error { }

async function get(url, refreshTokensIfNecessary = true)
{
    return await request(url, 'GET', null, refreshTokensIfNecessary);
}

async function post(url, data, refreshTokensIfNecessary = true)
{
    return await request(url, 'POST', data, refreshTokensIfNecessary);
}

async function request(url, method, data, refreshTokensIfNecessary)
{
    let result = { status: -1, message: null }
    
    try
    {
        let options = {
            body: data != null ? JSON.stringify(data) : null,
            method: method,
            headers: {
                'ContentType': 'application/json',
                'Accepts': 'application/json',
            }
        };
        
        let response = await fetch(api(url), options);
        if (response.status == 401 && refreshTokensIfNecessary)
            throw new UnauthorizedError();
    
        result.status = response.status;
    
        let json = await response.json();
        result.message = json.messages != undefined ? json.messages[0] : json;

        return result;
    }
    catch (error)
    {
        if (!(error instanceof UnauthorizedError))
            return result;

        let refreshResult = await request('auth/tokens/refresh-web.php', 'POST', null, false);
        if (refreshResult.status != 200)
        {
            result.message = "You must login again";
            return result;
        }
        
        return await request(url, method, data);
    }
}

function createErrorMessage(message) 
{
    let errorMessage = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' + message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    return errorMessage;
}

function createSuccessMessage(message) 
{
    let successMessage = '<div class="alert alert-success alert-dismissible fade show" role="alert">' + message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    return successMessage;
}