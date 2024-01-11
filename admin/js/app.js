class UnauthorizedError extends Error { }

async function get(url, sendToken = true)
{
    return await request(url, 'GET', null, sendToken);
}

async function post(url, data, sendToken = true)
{
    return await request(url, 'POST', data, sendToken);
}

async function request(url, method, data, sendToken = true, refreshTokensIfNecessary = true)
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

        if (sendToken)
        {
            let accessToken = localStorage.getItem('accessToken') ?? "";
            options.headers['Authorization'] = `Bearer ${accessToken}`;
        }
        
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

        let refreshResult = await request('auth/tokens/refresh-web.php', 'POST', null, true, false);
        if (refreshResult.status != 200)
        {
            result.message = "You must login again";
            return result;
        }
        
        localStorage.setItem('accessToken', refreshResult.message.accessToken);
        return await request(url, method, data, sendToken);
    }
}

function init()
{
    $('[data-toggle="tooltip"]').tooltip();
    console.log("Test");
}

$(init);
