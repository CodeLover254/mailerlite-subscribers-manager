<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mailerlite Subscribers Manager</title>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>
<body>
<div class="wrapper">
    <div class="auth-form-container">
        <form action="{{route('validate-api-key')}}" method="post">
            <div class="auth-form-header">
                Mailerlite Subscriber Manager
            </div>
            <div class="auth-form-body">
                <label for="api-key-field" class="api-key-label">API Key</label>
                <input type="text" name="api_key" id="api-key-field" required>
            </div>
            <div class="auth-form-footer">
                <button class="submit-button" type="submit">Authorise</button>
                @if($errors->any())
                    <span class="err-message">{{$errors->first()}}</span>
                @endif
                @if(session()->has('error'))
                    <span class="err-message">{{session()->get('error')}}</span>
                @endif
            </div>
        </form>
    </div>
</div>
</body>
</html>
