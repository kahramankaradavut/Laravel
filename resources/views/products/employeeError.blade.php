<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.semanticui.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">


    <title>Employee Table</title>

    <style>
        nav {
            background-color: #333;
            padding: 10px;
        }

        nav ul {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            justify-content: space-around;
        }

        nav ul li {
            position: relative;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
        }

        nav ul li a:hover {
            background-color: #5f5d5d;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #5f5d5d;
            padding: 0;
        }

        .dropdown-menu li {
            width: 100%;
        }

        .dropdown-menu a {
            color: #fff;
            padding: 10px 20px;
            display: block;
            text-decoration: none;
        }

        .dropdown-menu a:hover {
            background-color: #777;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>

    <nav>
        <ul>
            <li>
                <<a href="javascript:history.back()" class="menubar">Back</a>
            </li>
            <li><a href="{{ route('importpage') }}" class="menubar">Main Page</a></li>
            <li><a href="{{ route('all.projects') }}" class="menubar">Details of All Projects</a></li>
            <li><a href="{{ route('all.employees') }}" class="menubar">Details of All Employees</a></li>
        </ul>
    </nav>

</head>

<body>




    <div class="header">
        @if (isset($errorMessage))
            <div class="alert alert-danger">
                {{ $errorMessage }}
            </div>
        @endif
    </div>
</body>

</html>
