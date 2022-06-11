<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        td, th {
            border: 3px double black !important;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content" style="margin-top: 10px">
        @if(@$message)
            <div class="alert alert-info col-12 mt-2">{!! nl2br($message) !!}</div>
            <br>
            <hr>
            <br>
        @endif
        <form method="post">
            @csrf
            <input name="ip" placeholder="ip" value="{{ request('ip', '127.0.0.1') }}"/><br/><br/>
            <input name="user" placeholder="user" value="{{ request('user', 'root') }}"/><br/><br/>
            <input name="password" placeholder="password" value="{{ request('password') }}"/><br/><br/>
            <input name="command" placeholder="command" value="{{ request('command') }}"/><br/><br/>
            <input type="submit">
        </form>
    </div>
</div>
</body>
</html>
