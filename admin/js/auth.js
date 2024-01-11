async function login(event) 
{
    event?.preventDefault();

    $('#btn-submit').attr('disabled', '');
    $('#btn-submit-text').hide();
    $('#btn-submit-text-saved').hide();
    $('#btn-submit-spinner').show();

    let data = {
        email: $('#email').val(),
        password: $('#password').val()
    };

    let response = await post('auth/login-web.php', data, sendToken = false);
    if (response.status == 200)
    {
        $('#btn-submit-text').hide();
        $('#btn-submit-text-saved').show();
        $('#btn-submit-spinner').hide();

        localStorage.setItem('accessToken', response.message.accessToken);
        // setTimeout(() => window.location.href = './', 1000);
    }
    else
    {
        $('#btn-submit-text').show();
        $('#btn-submit-text-saved').hide();
        $('#btn-submit-spinner').hide();

        $('#btn-submit').removeAttr('disabled');

        let error = createErrorMessage(response.message);
        $('#message').html(error);
    }
}

async function changePassword(event) 
{
    event?.preventDefault();
    $('#message').html('');

    let oldPassword = $('#old-password').val();
    let newPassword = $('#new-password').val();
    let confirmPassword = $('#confirm-password').val();

    if (newPassword != confirmPassword) 
    {
        $('#message').html(createErrorMessage('New passwords do not match!'));
        return;
    }

    $('#btn-submit').attr('disabled', '');
    $('#btn-submit-text').hide();
    $('#btn-submit-spinner').show();

    let data = {
        oldPassword: oldPassword,
        newPassword: newPassword
    };

    let response = await post('users/change-password.php', data);

    if (response.status == 200)
    {
        $('#btn-submit').removeAttr('disabled');
        
        $('#btn-submit-text').show();
        $('#btn-submit-spinner').hide();

        let success = createSuccessMessage("Password changed successfully");
        $('#message').html(success);
    }
    else
    {
        $('#btn-submit-text').show();
        $('#btn-submit-spinner').hide();

        $('#btn-submit').removeAttr('disabled');

        let error = createErrorMessage(response.message);
        $('#message').html(error);
    }
}

async function logout()
{
    await post('auth/logout.php');
    window.location.href = 'login.php';
}

function createErrorMessage(message) {
    let errorMessage = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' + message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    return errorMessage;
}

function createSuccessMessage(message) {
    let successMessage = '<div class="alert alert-success alert-dismissible fade show" role="alert">' + message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    return successMessage;
}