<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400,700">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">


</head>

<body>
    <div class="header">
        <h1>Piri Project Detailer</h1>
        <h4>View The Details Of Your Project Excel File</h4>
    </div>

    <form action="{{ route('import.excel', ['text_input' => 'TEXT_INPUT']) }}" method="POST"
        enctype="multipart/form-data" id="myForm">
        @csrf
        <div class="content">
            <p>1. Add a File</p>
            <p>2. Specify a Project Name</p>
            <p>3. Upload Your File</p>

            <div id="fileDropArea" class="file-drop-area">
                <span class="file-drop-text" id="fileDropText">Click to select file</span>
                <input type="file" id="excel-import" name="excel_file" accept=".xlsx, .xls, .tsv" required>
            </div>

            <input type="text" id="textInput" name="text_input" placeholder="Enter project name" required>
            <button type="submit" class="custom-button">Upload</button>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div id="result"></div>
        </div>
    </form>

    <script>
        document.getElementById('myForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var textInputValue = encodeURIComponent(document.getElementById('textInput').value);
            var formAction = "{{ route('import.excel', ['text_input' => 'TEXT_INPUT']) }}".replace('TEXT_INPUT',
                textInputValue);
            document.getElementById('myForm').action = formAction;


            this.submit();
        });
    </script>

    <div id="line"></div>
    <form action="{{ route('show.table') }}" method="GET" enctype="multipart/form-data">
        <div class="content">
            <p>Access preloaded project files.</p>
            <div class="custom-select">
                <select name="textInput" aria-label="Default select example" id="items">
                    <option disabled selected>Select the project name you want to view</option>
                    @foreach ($inputs as $input)
                        <option value="{{ $input->uid }}" >{{ $input->name }} - - - - - ->   {{ $input->updated_at->format('d.m.Y') }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="custom-button">Show</button>
        </div>
    </form>

    <script>
        const fileInput = document.getElementById('excel-import');
        const fileDropText = document.getElementById('fileDropText');

        fileInput.addEventListener('change', function() {
            const fileName = this.value.split('\\').pop();
            fileDropText.textContent = fileName;
        });
    </script>
</body>

</html>
