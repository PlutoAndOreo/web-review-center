<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Upload Video</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px; }
        .container { max-width: 500px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        input[type="file"] { display: block; margin: 20px auto; }
        button { background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; }
        button:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Upload Video</h2>
        <form action="{{ url('/upload-video') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="video" accept="video/mp4" required>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
