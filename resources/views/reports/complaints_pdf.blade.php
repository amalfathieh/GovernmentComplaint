<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; }
        th { background-color: #f2f2f2; }
        @font-face {
            font-family: 'DejaVu';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/DejaVuSans.ttf') }}") format('truetype');
        }

        body {
            font-family: 'DejaVu';
            direction: rtl;
            text-align: right;
        }
    </style>
</head>
<body>
<h2>تقرير الشكاوى الحكومية</h2>
<table>
    <thead>
    <tr>
        <th>رقم الشكوى</th>
        <th>اسم المواطن</th>
        <th>الجهة الحكومية</th>
        <th>نوع الشكوى</th>
        <th>عنوان الشكوى</th>
        <th>الحالة</th>
        <th>الموقع</th>
        <th>تاريخ الإنشاء</th>
    </tr>
    </thead>
    <tbody>
    @foreach($complaints as $complaint)
        <tr>
            <td>{{ $complaint['رقم الشكوى'] }}</td>
            <td>{{ $complaint['اسم المواطن'] }}</td>
            <td>{{ $complaint['الجهة الحكومية'] }}</td>
            <td>{{ $complaint['نوع الشكوى'] }}</td>
            <td>{{ $complaint['عنوان الشكوى'] }}</td>
            <td>{{ $complaint['الحالة'] }}</td>
            <td>{{ $complaint['الموقع'] }}</td>
            <td>{{ $complaint['تاريخ الإنشاء'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
