<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <h3 style="font-weight: bold">Data Employee IMP-Studio</h3>
    <table class="table table-bordered">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Unde cum magnam soluta modi quisquam, rerum, provident nemo, quaerat autem fuga harum libero vero eum. Illum aperiam veniam accusamus natus assumenda.
        <thead>
            <tr></tr>
            <tr></tr>
            <tr>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    No
                </th>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    Username
                </th>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    Email
                </th>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    Profile
                </th>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    Firstname
                </th>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    Lastname
                </th>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    Gender
                </th>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    ID Number
                </th>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    Division
                </th>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    Position
                </th>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    Birth Date
                </th>
                <th style="font-weight: bold;font-size:12px;text-align:center">
                    Address
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employee as $item)
                    <tr style="font-weight: bold">
                        <td style="width: 4;text-align:center;">{{ $loop->iteration }}</td>
                        <td style="width:24;text-align:center;">{{ $item->user->name }}</td>
                        <td style="width:24;text-align:center;">{{ $item->user->email }}</td>
                        <td style="width:20;text-align:center;">
                            {{ $item->avatar }}
                            <img height="50" src="storage/{{ $item->avatar }}" alt="">
                        </td>
                        <td style="width: 15;text-align:center;">{{ $item->first_name }}</td>
                        <td style="width: 15;text-align:center;">{{ $item->last_name }}</td>
                        <td style="width: 8;text-align:center;">{{ $item->gender }}</td>
                        <td style="width: 12;text-align:center;">{{ $item->id_number }}</td>
                        <td style="width: 20;text-align:center;">{{ $item->division->name }}</td>
                        <td style="width: 20;text-align:center;">{{ $item->position->name }}</td>
                        <td style="width: 20;text-align:center;">{{ $item->birth_date }}</td>
                        <td style="width: 60;text-align:center;">{{ $item->address }}</td>
                    </tr>
                @endforeach
        </tbody>

    </table>
</body>
</html>
