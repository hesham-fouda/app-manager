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

        td, th{
            border: 3px double black !important;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content" style="margin-top: 10px">
        @if(session()->has('message'))
        <div class="alert alert-info col-12 mt-2">{!! nl2br(session()->get('message')) !!}</div>
        <br>
        <hr>
        <br>
        @endif
        <table>
            <thead>
            <tr>
                <th>package name</th>
                <th>package description</th>
                <th>composer v</th>
                <th>installed v</th>
                {{--<th>latest v</th>--}}
                <th style="min-width: 200px">actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($packages as $package)
            <tr>
                <td>
                    <a href="https://github.com/{{$package['name']}}" target="_blank">{{ $package['name'] }}</a>
                </td>
                <td>{!! wordwrap($package['description'], 80, '<br>') !!}</td>
                <td>{{ $package['composer-version'] }}</td>
                <td>{{ $package['version'] }}</td>
                {{--<td></td>--}}
                <td>
                    <div class="links" style="margin: 5px">
                        <a href="{{ route($routeName.'.index', ['action' => 'composer', 'command' => 'update', 'package' => $package['name']]) }}">update</a>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
