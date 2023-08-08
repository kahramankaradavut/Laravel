<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.semanticui.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

    <title>Details of All Projects</title>

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
        </ul>
    </nav>

    <div class="header">

        <h1>Details of All Projects</h1>


    </div>


    <div class="container" style="margin-bottom: 100px;">
        <h2>Details of Projects</h2>
        <table class="ui celled table table-secondary" id="myTable" style="width: 100%">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Number of Employees</th>
                    <th>Total Tasks</th>
                    <th>Total Completed Tasks</th>
                    <th>Total Undelayed Tasks</th>
                    <th>Total Delayed Tasks</th>
                    <th>Success Rate</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($projectDetails as $projectDetail)
                
                    @foreach ($projectDetail as $stats)
                        @php
                            
                            if ($stats['success_rate'] >= 0 && $stats['success_rate'] <= 40) {
                                $completionStatusClass = 'bg-danger';
                            } elseif ($stats['success_rate'] >= 41 && $stats['success_rate'] <= 70) {
                                $completionStatusClass = 'bg-warning';
                            } elseif ($stats['success_rate'] >= 71 && $stats['success_rate'] <= 100) {
                                $completionStatusClass = 'bg-success';
                            }
                            
                        @endphp
                        <tr>
                            <td>
                                <a class="custom-link"
                                    href="{{ route('show.table', $stats['uid']) }}">{{ $stats['project_name'] }}</a>
                            </td>
                            <td>{{ $stats['employees_count'] }}</td>
                            <td>{{ $stats['total_tasks'] }}</td>
                            <td>{{ $stats['completed_tasks'] }}</td>
                            <td>{{ $stats['undelayed_tasks'] }}</td>
                            <td>{{ $stats['delayed_tasks'] }}</td>
                            <td class="{{ $completionStatusClass }}">
                                <span
                                    style="color: rgb(0, 0, 0)">{{ number_format($stats['success_rate'], 2) }}%</span>
                            </td>
                        </tr>
                    @endforeach
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
