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
    <h2>Adoption request: {{ $request->status?->value ?? 'Pending' }}</h2>
    <table>
        @foreach ([
            'Name' => $request->name,
            'Address' => $request->address,
            'Email' => $request->email,
            'Mobile phone' => $request->mobile_phone,
            'Home phone' => $request->home_phone,
            'Valid ID' => count($request->valid_id ?? []).' file(s)',
            'Cat' => $request->name_of_cat,
            'Breed' => $request->breed,
            'Age' => $request->approximate_age,
            'Sex' => $request->sex,
            'Color' => $request->color,
            'Pickup date' => $request->date_of_adoption?->format('F j, Y'),
            'Requested on' => $request->created_at?->format('F j, Y'),
            'Status' => $request->status?->value,
            'Approved on' => $request->approval_date?->format('F j, Y'),
            'Released on' => $request->Release_date?->format('F j, Y'),
        ] as $label => $value)
            @if (filled($value))
                <tr>
                    <th>{{ $label }}</th>
                    <td>{{ $value }}</td>
                </tr>
            @endif
        @endforeach
    </table>
</body>
</html>