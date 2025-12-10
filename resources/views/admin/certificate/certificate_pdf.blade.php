<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0; }
        body { margin: 0; padding: 0; }
        img {
            width: 100%;
            height: auto;
            object-fit: cover; /* benar-benar fullscreen */
            display: block;
        }
    </style>
</head>
<body>
    <img src="{{ public_path($image) }}">
</body>
</html>
