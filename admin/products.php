<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri&family=Cairo:wght@200&family=Poppins:wght@100;200;300&family=Tajawal:wght@300&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | المنتجات </title>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }
        h3 {
            font-weight: bold;
            color: #343a40;
            margin-bottom: 20px;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center; /* توسيط البطاقات أفقياً */
            gap: 20px; /* مسافة بين البطاقات */
            margin-top: 20px;
        }
        .card {
            width: 18rem; /* زيادة طفيفة في عرض البطاقة */
            margin: 0; /* إزالة الهوامش الفردية للبطاقات */
            box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.05); /* إضافة ظل خفيف */
            border: 1px solid #dee2e6; /* إضافة حد خفيف */
            border-radius: 0.5rem; /* حواف أكثر نعومة */
            transition: transform 0.2s ease-in-out; /* تأثير تحريك عند التhover */
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card img {
            width: 100%;
            height: 220px; /* زيادة طفيفة في ارتفاع الصورة */
            object-fit: cover; /* احتواء الصورة بشكل أفضل */
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        .card-body {
            padding: 1rem;
            text-align: center;
        }
        .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            color: #212529;
        }
        .card-text {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .btn {
            margin: 0 0.25rem;
        }
        main {
            width: 90%; /* جعل الحاوية الرئيسية أوسع قليلاً */
            max-width: 1200px; /* تحديد أقصى عرض للحاوية الرئيسية */
            margin: 20px auto; /* توسيط الحاوية مع هوامش علوية وسفلية */
        }
        center {
            margin-bottom: 10px; /* إضافة مسافة أسفل عنوان الصفحة */
        }
    </style>
</head>
<body>
    <div class="container">
        <center>
            <h3> اتحكم بالمنتجات</h3>
        </center>
        <main>
            <div class="card-container">
                <?php
                include('config.php');
                $result = mysqli_query($con, "SELECT * FROM products");
                while($row = mysqli_fetch_array($result)){
                    echo "
                    <div class='card'>
                        <img src='$row[image]' class='card-img-top' alt='$row[name]'>
                        <div class='card-body'>
                            <h5 class='card-title'>$row[name]</h5>
                            <p class='card-text'>السعر: $row[price]</p>
                            <a href='delete.php?id=$row[id]' class='btn btn-danger btn-sm'>حذف منتج</a>
                            <a href='update.php?id=$row[id]' class='btn btn-primary btn-sm'>تعديل منتج</a>
                        </div>
                    </div>
                    ";
                }
                ?>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>