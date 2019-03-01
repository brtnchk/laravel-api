<!DOCTYPE html>
<html>
    <head>
        <title>Welcome Email</title>
    </head>

    <body>
        Dear {{ $user['username'] }},<br>
        <br>
        Thank you for purchasing a subscription to Memscore! Below you will find your login details:<br>
        <br>
        Username: {{ $user['email'] }}<br>
        Password: {{ $clearPassword }}<br>
        <br>
        Please confirm your account by clicking on this link:<br>
        <br>
        {{ 'https://' . env('API_HOST') }}/api/confirm/{{ $user->verifyUser->token }}?redirect=true<br>
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
