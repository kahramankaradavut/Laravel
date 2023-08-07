<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.semanticui.min.css">
    <link rel="stylesheet" href="{{asset('css/index.css')}}">

    <title>Person Details</title>

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
            <li><<a href="javascript:history.back()"  class="menubar">Back</a></li>
            <li><a href="{{ route('importpage') }}" class="menubar">Main Page</a></li>
            <li class="dropdown">
                <a href="#" class="menubar">All Employees ▼</a>
                <ul class="dropdown-menu">
                    @foreach ($employeesName as $name)
                        <li><a href="{{ route('person.details', ['personName' => $name->name]) }}">{{ $name->name }}</a></li>
                    @endforeach
                </ul>
            </li>
         
            
        </ul>
    </nav>

     {{-- Profil resmi çekme --}}
@php
$url = "https://github.com/".$personName;
$bot = file_get_contents($url);
preg_match_all('/src="([^"]*)"/i', $bot, $veri);
// echo "<pre>";
//     print_r ($veri);
// echo "</pre>";
$photo = $veri[1][39];
@endphp
   
<div class = "header">
    <h1>{{ $personName }}'s Details</h1>
</div>

    

    <div class="container" style="margin-bottom: 100px;">
        <h2>General Details of {{ $personName }}</h2>
        <table class="ui celled table table-secondary" style="width:100%">
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
                @php
                    if ($successRateGeneral >= 0 && $successRateGeneral <= 40) {
                        $completionStatusClass = 'bg-danger';
                    } elseif ($successRateGeneral >= 41 && $successRateGeneral <= 70) {
                        $completionStatusClass = 'bg-warning';
                    } elseif ($successRateGeneral >= 71 && $successRateGeneral <= 100) {
                        $completionStatusClass = 'bg-success';
                    }
                @endphp
                
                    <tr>
                        <td>
                            <img src="{{ $photo }}" alt="Profile Picture" width="100" height="100">
                        </td>
                        <td>{{ $personName }}</td>
                        <td>{{ $usageCount }}</td>
                        <td>{{ $completionStatusCompleted }}</td>
                        <td> {{ $completionStatusUndelayed}}</td>
                        <td>{{ $completionStatusDelayed }}</td>
                        <td class="{{ $completionStatusClass }}">
                            <span style="color: rgb(0, 0, 0)">{{ number_format($successRateGeneral, 2) }}%</span>
                        </td>
                    </tr>
               
            </tbody>
        </table>
    </div>

    <div class="container" style="margin-bottom: 100px;">
        <h2>{{ $personName }}'s Projects</h2>
        <table class="ui celled table table-secondary" style="width:100%">
            <thead>
                <tr>
                    <th>Project Names</th>
                    <th>Job Counts</th>
                    <th>Completed Tasks</th>
                    <th>Undelayed Tasks</th>
                    <th>Delayed Tasks</th>
                    <th>Success Rate</th>
                    
                </tr>
            </thead>
            <tbody>

                @foreach ($projectStatistics as $projectId => $stats)

@php

        $projectName = $abc->where('project_defination_id', $projectId)->first()->projectDefination->name;
        $projectUid = $abc->where('project_defination_id', $projectId)->first()->projectDefination->uid;
        
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
                            href="{{ route('show.table', $projectUid) }}">{{ $projectName }}</a>
                    </td>
                    <td>{{ $stats['total_tasks'] }}</td>
                    <td>{{ $stats['completed_tasks'] }}</td>
                    <td>{{ $stats['undelayed_tasks'] }}</td>
                    <td>{{ $stats['delayed_tasks'] }}</td>
                    <td class="{{ $completionStatusClass }}">
                        <span style="color: rgb(0, 0, 0)">{{ number_format($stats['success_rate'], 2) }}%</span>
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
<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
</script>
<script>
    $(document).ready(function() {
        $('#myTable2').DataTable();
    });
</script>
<div style="height: 1px;"></div>

</body>

</html>
