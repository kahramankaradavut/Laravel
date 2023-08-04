const fileInput = document.getElementById('excel-import');

fileInput.addEventListener('change', function() {
  const fileName = this.value.split('\\').pop();
  const textInput = document.getElementById('textInput');
  textInput.value = fileName;
});