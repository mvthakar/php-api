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

    let response = await post('auth/login-web.php', data, false);
    if (response.status == 200)
    {
        $('#btn-submit-text').hide();
        $('#btn-submit-text-saved').show();
        $('#btn-submit-spinner').hide();

        setTimeout(() => window.location.href = './index.php', 1000);
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
    await post('auth/logout.php', null, false);
    window.location.href = admin('login.php');
}
