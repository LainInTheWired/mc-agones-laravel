<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Image Display</title>
<!-- Tailwind CSSのリンク -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
<!-- 画像を表示するためのコンテナ -->
<div class="max-w-sm rounded overflow-hidden shadow-lg">
  <img class="w-full" src="/path_to_your_image.png" alt="Descriptive Alt Text">
  <div class="px-6 py-4">
    <div class="font-bold text-xl mb-2">Your Image Title</div>
    <p class="text-gray-700 text-base">
      Your image description goes here.
    </p>
  </div>
</div>
</body>
</html>
