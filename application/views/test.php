<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>My Select2 Example</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
  <script src="https://code.jquery.com/jquery-2.2.4.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>  
</head>
<body>
  Selections:
  <select id="example1" multiple>
    <option>Testing</option>
  </select>
</body>
</html>

<script>
    $(document).ready(function() {
    $('#example1').select2({
      placeholder: "Hello",
      width: '100px',
      multiple: true
    });
});
</script>