@include('scripts')
@include('admin.adminNavbar')
<style>
            /* Basic reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .containerAdmin {
            display: grid;
            grid-template-rows: auto 1fr auto;
            grid-template-columns: 200px 1fr;
            grid-template-areas:
                "header header"
                "sidebar main-content"
                "footer footer";
            height: 100vh;
            gap: 10px; /* Optional gap between items */
        }

        .header {
            grid-area: header;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }

        .sidebar {
            grid-area: sidebar;
            background-color: #e0e0e0;
            padding: 20px;
        }

        .main-content {
            grid-area: main-content;
            background-color: #ffffff;
            padding: 20px;
        }

        .footer {
            grid-area: footer;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   


</body>
</html>