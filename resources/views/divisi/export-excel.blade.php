<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h3 style="font-weight: bold">Data Division IMP-Studio</h3>
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>Name Division</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($division as $item)    
            <tr>
                <td>
                    {{ $loop->iteration }}
                </td>
                <td>
                    {{ $item->name }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>