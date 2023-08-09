<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.semanticui.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    <title>Details of All Employees</title>

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
</head>

<body>
    <nav>
        <ul>
            <li>
                <<a href="javascript:history.back()" class="menubar">Back</a>
            </li>
            <li><a href="{{ route('importpage') }}" class="menubar">Main Page</a></li>
            <li><a href="{{ route('all.projects') }}" class="menubar">Details of All Projects</a></li>
            <li class="dropdown">
                <a  class="menubar">All Projects ▼</a>
                <ul class="dropdown-menu">
                    @foreach ($inputs as $input)
                        <li><a href="{{ route('show.table', $input->uid) }}">{{ $input->name }}</a></li>
                    @endforeach
                </ul>
            </li>
        </ul>
    </nav>

    <div class="header">
        <h1>Details of All Employees</h1>
    </div>

    <div class="container" style="margin-bottom: 100px;">
        <h2>General Details of All Employees</h2>
        <table class="ui celled table table-secondary" style="width:100%" id="myTable">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Job Count</th>
                    <th>Completed Tasks</th>
                    <th>Undelayed Tasks</th>
                    <th>Delayed Tasks</th>
                    <th>Success Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employeesDetails as $name => $detail)
                    @php
                        if ($detail['success_rate'] >= 0 && $detail['success_rate'] <= 40) {
                            $completionStatusClass = 'bg-danger';
                        } elseif ($detail['success_rate'] >= 41 && $detail['success_rate'] <= 70) {
                            $completionStatusClass = 'bg-warning';
                        } elseif ($detail['success_rate'] >= 71 && $detail['success_rate'] <= 100) {
                            $completionStatusClass = 'bg-success';
                        }
                        $url = 'https://github.com/' . $detail['name'];
                        $bot = file_get_contents($url);
                        preg_match_all('/src="([^"]*)"/i', $bot, $veri);
                        // echo "<pre>";
                        //     print_r ($veri);
                        // echo "</pre>";
                        $photo = $veri[1][39];
                    @endphp

                    <tr>
                        <td>
                            <img src="{{ $photo }}" alt="Profile Picture" width="100" height="100">
                        </td>
                        <td>
                            <a class="custom-link"
                                href="{{ route('person.details', ['personName' => $detail['name']]) }}">{{ $detail['name'] }}</a>
                        </td>
                        <td>{{ $detail['job_count'] }}</td>
                        <td>{{ $detail['completed_task'] }}</td>
                        <td> {{ $detail['undelayed_task'] }}</td>
                        <td>{{ $detail['delayed_task'] }}</td>
                        <td class="{{ $completionStatusClass }}">
                            <span style="color: rgb(0, 0, 0)">{{ number_format($detail['success_rate'], 2) }}%</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.semanticui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                dom: 'flitBp',
                buttons: [{
                    extend: 'pdfHtml5',
                    download: 'open'
                }]
            });
        });
    </script>
        <div style="height: 1px;"></div>

</body>

</html>
