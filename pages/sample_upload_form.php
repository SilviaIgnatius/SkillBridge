<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Resource Upload Form</title>
</head>
<body>
    <h2>Upload Resources</h2>
    <form action="sample_upload_resource.php" method="post" enctype="multipart/form-data">
        <label for="skillName">Skill Name:</label>
        <input type="text" id="skillName" name="skillName" required><br><br>

        <label for="moduleName">Module Name:</label>
        <input type="text" id="moduleName" name="moduleName" required><br><br>

        <label for="videoName">Video Name:</label>
        <input type="text" id="videoName" name="videoName"><br><br>

        <label for="url">URL:</label>
        <input type="text" id="url" name="url"><br><br>

        <label for="pdfName">PDF Name:</label>
        <input type="text" id="pdfName" name="pdfName"><br><br>

        <label for="pdfFile">Upload PDF:</label>
        <input type="file" id="pdfFile" name="pdfFile"><br><br>

        <input type="submit" value="Upload Resource">
    </form>
</body>
</html>
