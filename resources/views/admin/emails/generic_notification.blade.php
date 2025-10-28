<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>{{ $title }}</h2>
    <p>{!! nl2br(e($messageContent)) !!}</p>
</body>

</html>