<!DOCTYPE html>
<html>
<head>
    <title>Adoption Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Approved Request</h2>
    <table>
        <tr>
            <th>Name</th>
            <td>{{ $request->name }}</td>
        </tr>
        <tr>
            <th>Address</th>
            <td>{{ $request->address }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $request->email }}</td>
        </tr>
        <tr>
            <th>Mobile Phone</th>
            <td>{{ $request->mobile_phone }}</td>
        </tr>
        <tr>
            <th>Valid ID</th>
            <td>{{ $request->valid_id }}</td>
        </tr>
        <tr>
            <th>Name of Cat</th>
            <td>{{ $request->name_of_cat }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $request->status }}</td>
        </tr>
        <tr>
            <th>Approval Date</th>
            <td>{{ $request->approval_date }}</td>
        </tr>
    </table>
</body>
</html>