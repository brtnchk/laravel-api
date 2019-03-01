<!DOCTYPE html>
<html>
    <head>
        <title>Memscore Password Reset</title>
    </head>

    <body>
    Dear {{ $user->username }},<br>
    <br>
    You have requested to reset the password for your account. Click here to set your new password:<br>
    {{ env('APP_CLIENT') }}/passwordreset?token={{ $token }}<br>
    <br>
    If you did not request a password reset, just ignore this message.<br>
    <br>
    If you need any help, instructions can be found here:<br>
    https://memscore.com/about/help<br>
    <br>
    If you don’t find the answer you are looking for, or need help in any way, please don't hesitate to reach out!
    Our Support Team can be contacted at: support@memscore.com<br>
    <br>
    Thank you again for your purchase, and we look forward to serving you for many years ahead.<br>
    <br>
    All the best,<br>
    Memscore
    </body>
</html>


