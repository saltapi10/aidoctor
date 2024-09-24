<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Medical Report</title>
</head>
<body>
<form action="/ask-ai-doc" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="file">Choose medical report (PDF, DOCX, TXT):</label>
    <input type="file" name="file" id="file" required>
    <button type="submit">Upload and Analyze</button>
</form>
</body>
</html>
