<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Universal Markets Inventory</title>

    <!-- Scripts -->

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{asset("js/scripts.js")}}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @auth
    <script>
        var apiToken = "{{Auth::user()->api_token}}"
    </script>
    @endauth
    @yield("head")
</head>
<body style="overflow: hidden">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom ">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    Universal Markets Inventory
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            <li class="nav-item">
                                @if (Route::has('register'))
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                @endif
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>


                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('jobs') }}">
                                        Jobs
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        @auth
        <nav class="navbar navbar-expand-md navbar-light bg-white justify-content-center shadow">
            <div class="col-10">
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link py-0" href="{{route("productRanges")}}">Product Ranges</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle py-0" data-toggle="dropdown">Products</a>
                            <div class="dropdown-menu">
                                <a href="{{route("products")}}" class="dropdown-item">Products</a>
                                <a href="{{route("productAttributes")}}" class="dropdown-item">Product Attributes</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle py-0" data-toggle="dropdown">Stock</a>
                            <div class="dropdown-menu">
                                <a href="{{route("stock.upload")}}" class="dropdown-item">Upload</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-0" href="{{route("jobs")}}">Jobs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-0" href="{{route("tests")}}">Tests</a>
                        </li>

                    </ul>
                </div>

            </div>
        </nav>
        @endauth
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
