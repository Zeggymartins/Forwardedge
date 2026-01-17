<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollments Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        h1 {
            font-size: 18px;
            margin: 0 0 6px;
        }
        .meta {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
            font-weight: 600;
        }
        tbody tr:nth-child(even) {
            background: #f9fafb;
        }
    </style>
</head>
<body>
    <h1>Course Enrollments</h1>
    <div class="meta">Generated at: {{ $generatedAt->format('Y-m-d H:i') }}</div>
    <table>
        <thead>
            <tr>
                <th>Enrollment ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Course Type</th>
                <th>Plan</th>
                <th>Total</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Verification</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row['Enrollment ID'] }}</td>
                    <td>{{ $row['Name'] }}</td>
                    <td>{{ $row['Email'] }}</td>
                    <td>{{ $row['Course'] }}</td>
                    <td>{{ $row['Course Type'] }}</td>
                    <td>{{ ucfirst($row['Plan']) }}</td>
                    <td>{{ $row['Total'] }}</td>
                    <td>{{ $row['Balance'] }}</td>
                    <td>{{ ucfirst($row['Status']) }}</td>
                    <td>{{ ucfirst($row['Verification Status']) }}</td>
                    <td>{{ $row['Created At'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">No enrollments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
