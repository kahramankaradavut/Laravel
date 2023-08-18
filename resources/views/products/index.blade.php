<!DOCTYPE html>
<html>

<head>
    {{-- <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.semanticui.min.css">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">


    <title>Project Table</title>

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
    @if (env('SHOW_PROJECT'))
        <nav>
            <ul>
                <li><a id="copyLinkButton" class="menubar">Copy Page Link</a></li>
                <li><a href="{{ route('importpage') }}" class="menubar">Main Page</a></li>
                <li><a href="{{ route('all.projects') }}" class="menubar">Details of All Projects</a></li>
                <li><a href="{{ route('all.employees') }}" class="menubar">Details of All Employees</a></li>
                <li class="dropdown">
                    <a class="menubar">All Projects ▼</a>
                    <ul class="dropdown-menu">
                        @foreach ($inputs as $input)
                            <li><a href="{{ route('show.table', $input->uid) }}">{{ $input->name }}</a></li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        </nav>
    @else
        <nav>
            <ul>
                <li><a id="copyLinkButton" class="menubar">Copy Page Link</a></li>
                <li><a href="{{ route('importpage') }}" class="menubar">Main Page</a></li>
            </ul>
        </nav>
    @endif
    <div class="header">
        <h1>Project Name: {{ $textInput }}</h1>

        <button id="deleteButton" class="btn bg-danger" style="color: #fff; margin-bottom: 10px;">Delete
            Project</button>

        <div class="container" style="margin-bottom: 10px; margin-top: 10px">
            <table class="ui celled table table-secondary display" style="width: 100%" id="myTable3">
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
                    @php
                        $undelayed = $products->where('complation_status_color', 1)->count();
                        $delayed = $products->where('complation_status_color', 2)->count();
                        $completedTasks = $products->where('status', 'Done')->count();
                        $finished = $completedTasks - ($undelayed + $delayed);
                        
                        $completionPercentage = $completedTasks > 0 ? round((($finished * env('DIFFERENCE_MULTIPLIER') + $delayed * env('DELAY_MULTIPLIER') + $undelayed * env('UNDELAY_MULTIPLIER')) / $completedTasks) * 100, 2) : 0;
                        
                        $colorClass = '';
                        if ($completionPercentage >= 0 && $completionPercentage <= 40) {
                            $colorClass = 'bg-danger';
                        } elseif ($completionPercentage >= 41 && $completionPercentage <= 70) {
                            $colorClass = 'bg-warning';
                        } elseif ($completionPercentage >= 71 && $completionPercentage <= 100) {
                            $colorClass = 'bg-success';
                        }
                    @endphp

                    <td>{{ $textInput }}</td>
                    <td>{{ count($usageCount) }}</td>
                    <td>{{ count($products) }}</td>
                    <td>{{ $completedTasks }}</td>
                    <td>{{ $undelayed }}</td>
                    <td>{{ $delayed }}</td>
                    <td class="{{ $colorClass }}">
                        <div>
                            <span style="color: rgb(0, 0, 0)">{{ number_format($completionPercentage, 2) }}%</span>
                        </div>
                    </td>
                </tbody>
            </table>
        </div>

    </div>
    </div>



    <!-- Ürün listesi tablosu -->
    <div class="container" style="margin-bottom: 100px;">
        <h2>Assignment Table</h2>

        <table class="ui celled table" id="myTable" style="width:100%">
            <!-- Tablo başlıkları -->
            <thead>
                <tr>
                    <th>Task ID</th>
                    <th>Title</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Label</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Due Date</th>
                    <th>Delay Days</th>
                </tr>
            </thead>
            <!-- Tablo içeriği -->
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->title }}</td>
                        <td>{{ $product->assignees }}</td>
                        <td>{{ $product->status }}</td>
                        <td>{{ $product->labels }}</td>
                        <td>{{ $product->start_date }}</td>
                        <td>{{ $product->end_date }}</td>
                        <td>{{ $product->due_date }}</td>
                        <td
                            class="{{ $product->complation_status_color == 1 ? 'bg-success' : ($product->complation_status_color == 2 ? 'bg-danger' : '') }}">
                            @if ($product->complation_status_color !== null)
                                <span style="color: black;">{{ $product->complation_status }}</span>
                            @else
                                {{ $product->complation_status }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Başarı Tablosu -->
    <div class="container" style="margin-bottom: 100px;">
        <h2>Employee Report Table</h2>
        <table class="ui celled table" style="width:100%" id="myTable2">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Job Count</th>
                    <th>Completed Tasks</th>
                    <th>Undelayed Tasks</th>
                    <th>Delayed Tasks</th>
                    <th>Success Rate</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($usageCount as $name => $count)
                    @php
                        $undelayed = $completionStatus[$name]['undelayed'];
                        $delayed = $completionStatus[$name]['delayed'];
                        $completedTasks = $completionStatus[$name]['completed'];
                        $finished = $completedTasks - ($undelayed + $delayed);
                        
                        $completionPercentage = $completedTasks > 0 ? round((($finished * env('DIFFERENCE_MULTIPLIER') + $delayed * env('DELAY_MULTIPLIER') + $undelayed * env('UNDELAY_MULTIPLIER')) / $completedTasks) * 100, 2) : 0;
                        
                        $colorClass = '';
                        if ($completionPercentage >= 0 && $completionPercentage <= 40) {
                            $colorClass = 'bg-danger';
                        } elseif ($completionPercentage >= 41 && $completionPercentage <= 70) {
                            $colorClass = 'bg-warning';
                        } elseif ($completionPercentage >= 71 && $completionPercentage <= 100) {
                            $colorClass = 'bg-success';
                        }
                    @endphp
                    <tr>
                        @if (env('SHOW_PROJECT'))
                            <td>
                                <a class="custom-link"
                                    href="{{ route('person.details', ['personName' => $name]) }}">{{ $name }}</a>
                            </td>
                        @else
                            <td>{{ $name }}</td>
                        @endif
                        <td>{{ $count }}</td>
                        <td>{{ $completedTasks }}</td>
                        <td>{{ $undelayed }}</td>
                        <td>{{ $delayed }}</td>
                        <td class="{{ $colorClass }}">

                            <div>
                                <span style="color: rgb(0, 0, 0)">{{ number_format($completionPercentage, 2) }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>



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

            $(document).ready(function() {
                $('#myTable2').DataTable({
                    dom: 'flitBp',
                    buttons: [{
                        extend: 'pdfHtml5',
                        download: 'open'
                    }]
                });
            });

            $(document).ready(function() {
                $('#myTable3').DataTable({
                    dom: 'tB',
                    buttons: [{
                        extend: 'pdfHtml5',
                        download: 'open'
                    }]
                });
            });


            document.getElementById("copyLinkButton").addEventListener("click", function() {
                var linkToCopy = "{{ route('show.table', $uidURL) }}"
                var tempInput = document.createElement("input");
                document.body.appendChild(tempInput);
                tempInput.value = linkToCopy;
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);


                var alertTimeout = 2000; // 1 saniye

                var alertElement = document.createElement("div");
                alertElement.style.position = "fixed";
                alertElement.style.top = "50%";
                alertElement.style.left = "50%";
                alertElement.style.transform = "translate(-50%, -50%)";
                alertElement.style.backgroundColor = "#28a745";
                alertElement.style.padding = "10px";
                alertElement.style.color = "white";
                alertElement.style.borderRadius = "5px";
                alertElement.style.boxShadow = "0px 0px 5px rgba(0, 0, 0, 0.5)";
                alertElement.textContent = "Link Copied!";
                document.body.appendChild(alertElement);

                setTimeout(function() {
                    alertElement.remove();
                }, alertTimeout);
            });

            document.getElementById("deleteButton").addEventListener("click", function() {

                var password = prompt("Şifreyi Girin:");

                if (typeof password != "string" || password.trim() == "") {
                    return
                }
                var project_name = "{{ $textInput }}";
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ route('deleteDataProject') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        password: password,
                        project_name: project_name,
                    },
                    success: function(response) {
                        if (response == 1) {
                            alert('Project Deleted Successfully!');


                            setTimeout(function() {
                                window.location.href = '{{ route('importpage') }}';
                            }, 1000);
                        } else {
                            var alertTimeout = 2000; // 1 saniye

                            var alertElement = document.createElement("div");
                            alertElement.style.position = "fixed";
                            alertElement.style.top = "50%";
                            alertElement.style.left = "50%";
                            alertElement.style.transform = "translate(-50%, -50%)";
                            alertElement.style.backgroundColor = "#dc3545";
                            alertElement.style.padding = "10px";
                            alertElement.style.color = "white";
                            alertElement.style.borderRadius = "5px";
                            alertElement.style.boxShadow = "0px 0px 5px rgba(0, 0, 0, 0.5)";
                            alertElement.textContent = "Wrong Password!";
                            document.body.appendChild(alertElement);

                            setTimeout(function() {
                                alertElement.remove();
                            }, alertTimeout);
                        }

                    },
                    error: function(xhr, status, error) {
                        alert('Something went wrong ' + error);
                    }
                });
            });
        </script>
        <div style="height: 1px;"></div>
</body>

</html>
