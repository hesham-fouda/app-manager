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
    <div class="content" style="margin-top: 10px;width: 90%">
        @if(!empty(@$message ?? session('message')))
            <div style="border: solid green 2px;"
                 class="alert alert-info col-12 mt-2">{!! nl2br(@$message ?? session('message')) !!}</div>
            <br>
            <hr>
            <br>
        @endif
        <form method="post">
            @csrf
            <label for="lic_data">New Licence Data</label>
            <textarea name="lic_data" id="lic_data"
                      style="width: 100%;max-width: 100%;min-width: 100%;height: 200px;max-height: 200px;min-height: 200px"
            ></textarea>
            <input type="submit" value="Update">
        </form>
        @isset($licData)
            <hr>
            <textarea
                    style="width: 100%;max-width: 100%;min-width: 100%;height: 200px;max-height: 200px;min-height: 200px"
                    readonly>{{ $licData }}</textarea>
        @endisset
    </div>
</div>
</body>
</html>
