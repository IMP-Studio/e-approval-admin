<style type="text/css">
    body{
        font-family: Arial, Helvetica, sans-serif
    }
     table {
            width: 100%;
            border-collapse: collapse
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            margin: auto;
            text-align: left;
        }
        .img{
            margin: auto
        }
</style>
<body>
    <center>
        <h1>Data Employee IMP-Studio</h1>
    </center>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>
                    No
                </th>
                <th>
                    Username
                </th>
                <th>
                    Profile
                </th>
                <th>
                    Firstname
                </th>
                <th>
                    Lastname
                </th>
                <th>
                    Gender
                </th>
                <th>
                    Staff ID
                </th>
                <th>
                    Division
                </th>
                <th>
                    Position
                </th>
                <th>
                    Address
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($employee as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->user->name }}</td>
                    <td class="img">
                        <img height="50" src="storage/{{ $item->avatar }}" alt="">
                    </td>
                    <td>{{ $item->firstname }}</td>
                    <td>{{ $item->lastname }}</td>
                    <td>{{ $item->gender }}</td>
                    <td>{{ $item->id_number }}</td>
                    <td>{{ $item->division->name }}</td>
                    <td>{{ $item->position->name }}</td>
                    <td>{{ $item->address }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>
</body>
