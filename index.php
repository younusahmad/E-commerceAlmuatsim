<?php

include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
};

if (isset($_GET['logout'])) {
    unset($user_id);
    session_destroy();
    header('location:login.php');
};

if (isset($_POST['add_to_cart'])) {

    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

    if (mysqli_num_rows($select_cart) > 0) {
        $message[] = 'المنتج أضيف بالفعل إلى عربة التسوق!';
    } else {
        mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, image, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')") or die('query failed');
        $message[] = 'المنتج يضاف الى عربة التسوق!';
    }
};

if (isset($_POST['update_cart'])) {
    $update_quantity = $_POST['cart_quantity'];
    $update_id = $_POST['cart_id'];
    mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('query failed');
    $message[] = 'تم تحديث كمية سلة التسوق بنجاح!';
}

if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'") or die('query failed');
    header('location:index.php');
}

if (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    header('location:index.php');
}

// معالجة تقديم طلب الشراء عبر الواتساب (تم تعديل هذه الجزئية لتناسب الزر الجديد)
if (isset($_POST['place_order_whatsapp'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment = mysqli_real_escape_string($conn, $_POST['payment']);
    $total_products = '';
    $total_price = $_POST['grand_total'];

    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($cart_query) > 0) {
        while ($cart_item = mysqli_fetch_assoc($cart_query)) {
            $total_products .= $cart_item['name'] . ' (' . $cart_item['quantity'] . '), ';
        }
    }

    $message_wa = "طلب جديد:\nالاسم: $name\nرقم الهاتف: $phone\nالعنوان: $address\nطريقة الدفع: $payment\nالمبلغ الإجمالي: $total_price$\nالمنتجات: " . rtrim($total_products, ', ');
    $encoded_message = urlencode($message_wa);
    $whatsapp_number = '967776187275'; // تعديل: يجب أن يكون هذا الرقم الدولي مع رمز البلد
    $whatsapp_url = "whatsapp://send?phone=$whatsapp_number&text=$encoded_message";
    header("Location: $whatsapp_url");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri&family=Cairo:wght@200&family=Poppins:wght@100;200;300&family=Tajawal:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عربة التسوق</title>
</head>

<body>

    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '<div class="message" onclick="this.remove();">' . $message . '</div>';
        }
    }
    ?>


    <div class="container" id="container">>
        <div class="user-profile">
            <?php
            $select_user = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$user_id'") or die('query failed');
            if (mysqli_num_rows($select_user) > 0) {
                $fetch_user = mysqli_fetch_assoc($select_user);
            };
            ?>
            <p>المستخدم الحالي : <span><?php echo $fetch_user['name']; ?></span> </p>
            <div class="flex">
                <a href="index.php?logout=<?php echo $user_id; ?>" onclick="return confirm('هل أنت متأكد أنك تريد تسجيل الخروج؟');" class="delete-btn">تسجيل الخروج</a>
            </div>
        </div>
        <div class="header-navigation">
            <div class="navigation-buttons">
                <a href="#footer">تواصل معنا </a>
                <a href="#about-us">نبذة عنا</a>
                <a href="#order">الدفع</a>
                <a href="#shopping-cart">السلة</a>
                <a href="#products">المنتجات</a>
                <a href="#container">الصفحة الرئيسبة</a>

            </div>
        </div>

        <div class="main" id="main">
            <h1><span>الشهاب تك للإكترونيات وطاقة</span></h1>
            <img src="FB_IMG_1745528042606.jpg" alt="logo" width="600px">
            <br>
            <h2 class="weicom"> مرحبا بكم في متجر الشهاب تك الشركة الرائدة في مجال انظمة الطاقة البديله</h2>
        </div>

        <div class="ads-container">
    <div class="ads-track">
        <img src="a.jpg" alt="إعلان 1">
        <img src="aa.jpg" alt="إعلان 2">
        <img src="aaa.jpg" alt="إعلان 3">
        <img src="aaaa.jpg" alt="إعلان 4">
        <img src="aaaaa.jpg" alt="إعلان 5">
        <img src="aaaaaa.jpg" alt="إعلان 6">
        <img src="FB_IMG_1745528042606.jpg" alt="إعلان 6">


        </div>
</div>


        <div class="products" id="products">

            <h1 class="heading">Available products | المنتجات المتوفرة</h1>

            <div class="box-container">

                <?php
                include('config.php');
                $result = mysqli_query($conn, "SELECT * FROM products");
                while ($row = mysqli_fetch_array($result)) {
                    ?>
                    <form method="post" class="box" action="">
                        <img src="admin/<?php echo $row['image']; ?>" width="200">
                        <div class="name"><?php echo $row['name']; ?></div>
                        <div class="price"><?php echo $row['price']; ?></div>
                        <input type="number" min="1" name="product_quantity" value="1">
                        <input type="hidden" name="product_image" value="<?php echo $row['image']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo $row['name']; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                        <input type="submit" value="إظافة الى عربة التسوق" name="add_to_cart" class="btn">


                    </form>
                    <?php
                };
                ?>

            </div>

        </div>

        <center>
            <div class="shopping-cart" id="shopping-cart">

                <h1 class="heading"> Shopping cart | عربة التسوق </h1>

                <table>
                    <thead>
                        <tr>
                            <th>الصورة</th>
                            <th>الاسم</th>
                            <th>السعر</th>
                            <th>العدد</th>
                            <th>السعر الكلي</th>
                            <th>العمل</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
                        $grand_total = 0;
                        if (mysqli_num_rows($cart_query) > 0) {
                            while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
                                ?>
                                <tr>
                                    <td><img src="admin/<?php echo $fetch_cart['image']; ?>" height="75" alt=""></td>
                                    <td><?php echo $fetch_cart['name']; ?></td>
                                    <td><?php echo $fetch_cart['price']; ?>$ </td>
                                    <td>
                                        <form action="" method="post">
                                            <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                                            <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                                            <input type="submit" name="update_cart" value="تعديل" class="option-btn">
                                        </form>
                                    </td>
                                    <td><?php echo $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>$</td>
                                    <td><a href="index.php?remove=<?php echo $fetch_cart['id']; ?>" class="delete-btn" onclick="return confirm('إزالة العنصر من سلة التسوق؟');">حذف</a></td>
                                </tr>
                                <?php
                                $grand_total += $sub_total;
                            }
                        } else {
                            echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="6">العربة فارغة</td></tr>';
                        }
                        ?>
                        <tr class="table-bottom">
                            <td colspan="4">المبلغ الإجمالي :</td>
                            <td><?php echo $grand_total; ?>$</td>
                            <td><a href="index.php?delete_all" onclick="return confirm('حذف كل المنتجات من العربة?');" class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">حذف الكل</a></td>
                        </tr>
                    </tbody>
                </table>

                <div class="checkout-btn">
                    <a href="#order" class="btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">Proceed to Checkout | إتمام الشراء</a>
                </div>
                <hr>
                <hr>

                <section class="order-form" id="order">
                    <h1 class="heading">Place Your Order | تقديم طلبك</h1>
                    <div class="box-container">
                        <form action="" method="POST" id="order-form">
                            <center>
                                <h3> بياناتك </h3>
                                <div class="inputBox">
                                    <span>الاسم الكامل :</span>
                                    <input type="text" name="name" required placeholder="أدخل اسمك">
                                </div>
                                <div class="inputBox">
                                    <span>رقم الهاتف :</span>
                                    <input type="number" name="phone" required placeholder="أدخل رقم هاتفك">
                                </div>
                                <div class="inputBox">
                                    <span>عنوان التوصيل :</span>
                                    <input type="text" name="address" required placeholder="أدخل عنوان التوصيل بالتفصيل">
                                </div>
                                <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>">

                                <h3> طريقة الدفع </h3>
                                <div class="inputBox">
                                    <span>اختر طريقة الدفع :</span>
                                    <select name="payment" class="box">
                                        <option value="الدفع عند الاستلام">الدفع عند الاستلام</option>
                                        <option value="تحويل بنكي">تحويل بنكي</option>
                                    </select>
                                </div>

                            </center>

                            <div class="summary">
                                <h3>ملخص طلبك</h3>
                                <p>المنتجات التي قمت بحجزها : <span>
                                            <?php
                                            $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
                                            $product_list = '';
                                            if (mysqli_num_rows($cart_query) > 0) {
                                                while ($cart_item = mysqli_fetch_assoc($cart_query)) {
                                                    $product_list .= $cart_item['name'] . ' (' . $cart_item['quantity'] . '), ';
                                                }
                                                echo rtrim($product_list, ', ');
                                            } else {
                                                echo 'لا يوجد منتجات في العربة';
                                            }
                                            ?>
                                        </span></p>
                                <p>المبلغ الإجمالي : <span><?php echo $grand_total; ?>$</span></p>
                                <input type="submit" value="تأكيد الطلب عبر واتساب" name="place_order_whatsapp" class="btn">
                            </div>
                            <div id="payment-details"></div>
                        </form>
                    </div>
                </section>

                <script>
                    const form = document.getElementById('order-form');
                    const radios = document.querySelectorAll('select[name="payment"]');
                    const details = document.getElementById('payment-details');

                    function updatePaymentDetails(method) {
                        if (method === 'تحويل بنكي') {
                            details.innerHTML = '<strong>التحويل البنكي:</strong> &nbsp;بنك الشرق &nbsp;- &nbsp;رقم الحساب: <b>443567867</b>';
                        } else {
                            details.innerHTML = '<strong>الدفع عند الاستلام:</strong> سيتم التواصل معك لتأكيد الطلب وتحديد موعد التسليم.';
                        }
                    }

                    radios.forEach(radio => {
                        radio.addEventListener('change', () => updatePaymentDetails(radio.value));
                    });

                    updatePaymentDetails(radios[0].value); // عرض التفاصيل الافتراضية

                </script>
</div>

                <section class="about-us" id="about-us">
                    <h2 class="about-us-heading">نبذة عن الشهاب تك</h2>
                    <p class="about-us-content">
                       مرحباً بكم في الشهاب تك، وجهتكم الرائدة في عالم الإلكترونيات وأنظمة الطاقة البديلة.
                       نحن نفخر بتقديم مجموعة واسعة من المنتجات عالية الجودة التي تلبي احتياجاتكم المتنوعة،
                       سواء كنتم تبحثون عن أحدث التقنيات الإلكترونية أو حلول الطاقة المستدامة والموثوقة.
                       فريقنا ملتزم بتوفير أفضل المنتجات والخدمات لضمان تجربة تسوق ممتازة لعملائنا الكرام.
                       اكتشفوا مجموعتنا المتنوعة وابدأوا رحلتكم نحو مستقبل تكنولوجي مستدام مع الشهاب تك.
                   </p>
                </section>

                <section class="footer" id="footer">


                    <div class="share">
                        <a href="https://www.facebook.com/share/1EZvyKgeiE/" class="btn" style="background-color: #1877F2;" title="فيسبوك">
                            <i class="fab fa-facebook-f" style="font-size: 1.5rem;"></i>
                        </a>
                        <a href="#" class="btn" style="background-color: #000000;" title="إكس (تويتر سابقًا)">
                            <i class="fab fa-x-twitter" style="font-size: 1.5rem;"></i>
                        </a>
                        <a href="#" class="btn" style="background-color: #E4405F;" title="انستغرام">
                            <i class="fab fa-instagram" style="font-size: 1.5rem;"></i>
                        </a>
                        <a href="https://wa.me/967776187275" target="_blank" class="btn" style="background-color: #25D366;" title="واتساب">
                            <i class="fab fa-whatsapp" style="font-size: 1.5rem;"></i>
                        </a>
                        <a href="#" class="btn" style="background-color: #0077B5;" title="لينكدإن">
                            <i class="fab fa-linkedin-in" style="font-size: 1.5rem;"></i>
                        </a>
                    </div>
                    <h1 class="credit"> Designed and Developer By <span> Almuatsim Maodh </span> </h1>
                </section>


                <div class="almuatsim">



</body>
</center>

</html>
</center>
</div>
 <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap'); /* إضافة خط Cairo */

:root{
    --blue:#3498db;
    --red:#e74c3c;
    --orange:#f39c12;
    --black:#333;
    --white:#fff;
    --light-bg:#eee;
    --box-shadow:0 5px 10px rgba(0,0,0,.1);
    --border:2px solid var(--black);
}

*{
    font-family: 'Poppins', sans-serif;
    margin:0; padding:0;
    box-sizing: border-box;
    outline: none; border: none;
    text-decoration: none;
}

*::-webkit-scrollbar{
    width: 10px;
    height: 5px;
}

*::-webkit-scrollbar-track{
    background-color: transparent;
}

*::-webkit-scrollbar-thumb{
    background-color: var(--blue);
}

body{
    background-image: linear-gradient(to bottom right,hsl(0, 65.70%, 54.30%),#352c2c); /* الحفاظ على التدرج اللوني الجميل */
    transition: background-color 0.3s ease;
    padding-top: 80px; /* هامش علوي لاستيعاب الشريط الثابت */
}

.message{
    position: sticky;
    top:0; left:0; right:0;
    padding:15px 10px;
    background-color: var(--white);
    text-align: center;
    z-index: 1000;
    box-shadow: var(--box-shadow);
    color:var(--black);
    font-size: 15px;
    text-transform: capitalize;
    cursor: pointer;
}

.btn,
.delete-btn,
.option-btn{
    display: inline-block; /* تصحيح الخطأ الإملائي */
    padding:10px 30px;
    cursor: pointer;
    font-size: 15px;
    color:var(--white);
    border-radius: 5px;
    text-transform: capitalize;
    transition: background-color 0.3s ease; /* إضافة انتقال لتأثير التمرير */
}

.btn:hover,
.delete-btn:hover,
.option-btn:hover{
    background-color: var(--black);
}

.btn{
    background-color: var(--blue);
    margin-top: 10px;
}

.delete-btn{
    background-color: var(--red);
}

.option-btn{
    background-color: var(--orange);
}

/* تنسيقات شريط التنقل العلوي الثابت والمحسن مع أزرار أكبر */
.header-navigation {
    background-color: rgba(249, 247, 247, 0.9);
    color: white;
    padding: 18px 25px;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    transition: background-color 0.3s ease-in-out;
}

.header-navigation:hover {
    background-color: #333;
}

.header-navigation .navigation-buttons {
    display: flex;
    justify-content: center;
    gap: 60px;
}

.header-navigation .navigation-buttons a {
    font-family: 'Cairo', sans-serif; /* استخدام خط Cairo */
    color: white;
    text-decoration: none;
    padding: 20px 27px;
    border-radius: 16px;
    background-color:rgb(72, 61, 61);
    transition: background-color 0.3s ease-in-out, transform 0.2s ease-out, box-shadow 0.2s ease-out;
    box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
    font-size: 1.6rem; /* تقليل حجم الخط قليلاً ليتناسب مع الأزرار الكبيرة */
}

.header-navigation .navigation-buttons a:hover {
    background-color:rgb(246, 17, 17);
    transform: translateY(-3px);
    box-shadow: 0 5px 7px rgba(0, 0, 0, 0.35);
}

.header-navigation .navigation-buttons a:active {
    transform: translateY(0);
    box-shadow: 0 3px 5px rgba(0, 0, 0, 0.25);
}

/* تنسيقات عامة للجسم والمحتوى الرئيسي */
.container {
    max-width: 1200px;
    margin:  auto;
    padding: 20px;
}

.user-profile {
    background-color: #fff;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.user-profile:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.user-profile p {
    font-size: 1.6rem;
    color: #333;
    margin-bottom: 10px;
}

.user-profile .flex {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.delete-btn { /* تكرار تعريف الزر، سيتم استخدام التعريف الأول */
    display: inline-block;
    padding: 10px 20px;
    background-color: #dc3545;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 1.4rem;
    transition: background-color 0.3s ease, transform 0.2s ease-in-out;
}

.delete-btn:hover {
    background-color: #c82333;
    transform: scale(1.05);
}

.main {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    text-align: center;
    margin-bottom: 30px;
    animation: fadeIn 0.5s ease-out forwards;
}

.main h1 {
    color: red;
    font-weight: bold;
    font-size: 4rem;
    margin-bottom: 20px;
}

.main img {
    max-width: 150%;
    height: auto;
    border-radius: 5px;
    margin-bottom: 20px;
}

.main .weicom {
    color: red;
    font-weight: bold;
    font-size: 2rem;
}

/* --------------- قسم الإعلانات المتحرك --------------- */
.ads-container {
    width: 100%;
    overflow: hidden; /* إخفاء الصور التي تخرج عن الحاوية */
    margin-bottom: 30px; /* إضافة مساحة أسفل قسم الإعلانات */
    background-color: #f8f8f8; /* خلفية خفيفة لقسم الإعلانات */
    padding: 10px 0; /* بعض الحشو العلوي والسفلي */
    border-radius: 10px;
}

.ads-track {
    display: flex; /* ترتيب الصور جنبًا إلى جنب */
    animation: scrollAds 20s linear infinite; /* تطبيق حركة التحريك */
}

.ads-track img {
    height: 300px; /* ارتفاع صور الإعلانات (يمكنك تعديله) */
    margin-right: 20px; /* مساحة بين الصور */
    flex-shrink: 0; /* منع الصور من التقلص */
}

/* إزالة الهامش الأخير للصورة الأخيرة */
.ads-track img:last-child {
    margin-right: 0;
}

/* تعريف حركة التحريك */
@keyframes scrollAds {
    0% {
        transform: translateX(100%); /* تبدأ من خارج الشاشة على اليمين */
    }
    100% {
        transform: translateX(-100%); /* تنتهي خارج الشاشة على اليسار */
    }
}

/* استجابة للشاشات الأصغر */
@media (max-width: 768px) {
    .ads-track {
        animation-duration: 30s; /* إبطاء الحركة على الشاشات الأصغر */
    }

    .ads-track img {
        height: 80px; /* تصغير حجم الصور على الشاشات الأصغر */
        margin-right: 15px;
    }
}

.products,
.shopping-cart,
.order-form {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.products .heading,
.shopping-cart .heading,
.order-form .heading {
    color: red;
    background: black;
    font-weight: bold;
    font-size: 3.0rem;
    border-bottom: 3px solid #eee;
    padding-bottom: 70px;
    margin-bottom: 50px;
    text-align: center;
    animation: fadeIn 0.5s ease-out forwards;


}

.products .box-container {
    display: grid; /* استخدام Grid Layout لترتيب أفضل */
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* تحديد عرض أدنى لكل عنصر وتوزيع العناصر تلقائيًا */
    gap: 20px; /* مسافة أكبر بين العناصر */
    justify-content: center; /* توسيط العناصر أفقياً */
    padding: 20px; /* إضافة بعض الحشو للحاوية */
}

.products .box {
    text-align: center;
    border-radius: 8px;
    /* تم إزالة box-shadow مؤقتًا لإضافة حواف أوضح */
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out, border 0.3s ease-in-out; /* إضافة انتقال للحواف */
    padding: 20px;
    background-color: var(--white);
    border: 1px solid #ddd; /* إضافة حافة افتراضية خفيفة */
}

.products .box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);
    border-color: var(--red); /* تغيير لون الحافة عند التمرير */
}

.products .box img {
    height: 200px;
    max-width: 100%;
    object-fit: contain; /* احتواء الصورة داخل الحاوية مع الحفاظ على نسب الأبعاد */
    margin-bottom: 15px;
    border-radius: 5px; /* إضافة بعض التقويس للصورة */
    transition: transform 0.2s ease-in-out;
}

.products .box img:hover {
    transform: scale(1.05); /* تكبير طفيف للصورة عند التمرير */
}

.products .box .name {
    font-size: 1.8rem; /* حجم خط أكبر لاسم المنتج */
    margin-bottom: 8px;
    color:var(--black);
    padding:5px 0;
    font-weight: bold; /* جعل اسم المنتج أكثر وضوحًا */
}

.products .box .price {
    position: absolute;
    top: 15px; /* تعديل الموضع */
    left: 15px; /* تعديل الموضع */
    transform: rotateZ(-10deg); /* تدوير أقل حدة */
    padding: 5px 15px;
    border-radius: 5px;
    background-color: var(--red);
    color:var(--white);
    font-size: 1.4rem; /* حجم خط معقول للسعر */
    font-weight: bold; /* جعل السعر أكثر وضوحًا */
}

.products .box input[type="number"] {
    margin:10px 0;
    width: 80px; /* تحديد عرض محدد لحقل الإدخال */
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1.4rem;
    color:var(--black);
    padding: 8px 10px;
    text-align: center;
}

.products .box .btn {
    display: inline-block;
    padding: 12px 20px; /* زيادة الحشو للزر */
    background-color: red;
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-size: 1.5rem;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* إضافة انتقال للظل */
    margin-top: 10px; /* إضافة هامش علوي للزر */
    box-shadow: 0 2px 4px hsla(0, 15.80%, 62.70%, 0.10); /* إضافة ظل خفيف للزر */
}

.products .box .btn:hover {
    background-color:rgb(207, 21, 21);
    transform: scale(1.02);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15); /* ظل أكثر بروزًا عند التمرير */
}

.shopping-cart {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    margin-bottom: 40px;
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    overflow-x: auto; /* إضافة شريط تمرير أفقي في حال تجاوز المحتوى */
}

.shopping-cart table {
    width: 100%;
    border-collapse: collapse;
}

.shopping-cart th,
.shopping-cart td {
    padding: 12px 10px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

.shopping-cart th {
    background-color: #f9f9f9;
    font-size: 1.6rem;
    font-weight: bold; /* جعل رؤوس الجدول أكثر وضوحًا */
}

.shopping-cart td {
    font-size: 1.4rem;
}

.shopping-cart td img {
    max-width: 80px;
    height: auto;
    vertical-align: middle;
    border-radius: 5px; /* إضافة بعض التقويس للصور في السلة */
    transition: transform 0.2s ease-in-out;
}

.shopping-cart td img:hover {
    transform: scale(1.1);
}

.shopping-cart td input[type="number"] {
    width: 70px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1.4rem;
    text-align: center;
}

.shopping-cart td .option-btn,
.shopping-cart td .delete-btn {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 1.3rem;
    text-decoration: none;
    margin: 5px;
    cursor: pointer;
    transition: transform 0.2s ease-in-out;
}

.shopping-cart td .option-btn {
    background-color: #007bff;
    color: white;
}

.shopping-cart td .option-btn:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

.shopping-cart td .delete-btn {
    background-color: #dc3545;
    color: white;
}

.shopping-cart td .delete-btn:hover {
    background-color: #c82333;
    transform: scale(1.05);
}

.shopping-cart .table-bottom td {
    font-weight: bold;
    font-size: 1.5rem; /* جعل الإجمالي أكثر وضوحًا */

}

.shopping-cart .checkout-btn{
    text-align:center
}

.checkout-btn .btn { /* تطبيق تنسيق الزر العام على زر الدفع */
    display: inline-block;
    padding: 12px 30px;
    background-color: var(--red);
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-size: 1.6rem;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    margin-top: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.checkout-btn .btn:hover {
    background-color:hsl(0, 78.00%, 50.20%);
    transform: scale(1.02);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}

/* --------------- نموذج الطلب --------------- */
.order-form {
    display: block;
    background-color: #f4f6f8;
    border-radius: 12px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    direction: rtl;
    font-family: 'Arial', sans-serif;
    animation: slideInUp 0.5s ease-out forwards;
    padding: 30px; /* إضافة حشو عام للنموذج */
    margin-bottom: 40px; /* إضافة هامش سفلي للنموذج */
}

.order-form .heading {
    background-color: black;
    color: red;
    font-weight: bold;
    font-size: 3.0rem;
    border-bottom: 3px solid rgb(57, 56, 56);
    padding-bottom: 20px;
    margin-bottom: 30px;
    text-align: center;
    margin: 30px auto; /* توسيط العنوان */
    padding: 40px 20px; /* تقليل الحشو الجانبي للعنوان */
    border-radius: 8px; /* إضافة حواف مستديرة للعنوان */
}

.order-form .box-container {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    align-items: flex-start;
    justify-content: space-between; /* توزيع العناصر أفقياً */
}

.order-form .box-container form,
.order-form .box-container .summary {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid #eee;
    padding: 30px;
}

.order-form .box-container form {
    flex: 1 1 450px;
}

.order-form .box-container form h3 {
    color: #555;
    text-align: right;
    margin-bottom: 20px;
    font-size: 2rem;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
    font-weight: bold;
}

.order-form .box-container form .inputBox {
    margin-bottom: 20px;
}

.order-form .box-container form .inputBox span {
    display: block;
    margin-bottom: 8px;
    font-size: 1.5rem;
    color: red;
    font-weight: bold;
}

.order-form .box-container form .inputBox input[type="text"],
.order-form .box-container form .inputBox input[type="number"],
.order-form .box-container form .inputBox input[type="email"],
.order-form .box-container form .inputBox select {
    width: 100%;
    padding: 12px;
    border: 1px solid red;
    border-radius: 6px;
    font-size: 1.5rem;
    color: #333;
    box-sizing: border-box;
    margin-top: 5px;
    transition: border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.order-form .box-container form .inputBox input:focus,
.order-form .box-container form .inputBox select:focus {
    border-color: red;
    outline: none;
    box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
}

.order-form .box-container form .btn {
    display: inline-block;
    padding: 14px 24px;
    background-color: red;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 1.7rem;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    width: 100%;
    margin-top: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.order-form .box-container form .btn:hover {
    background-color: rgb(235, 30, 30);
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(249, 14, 14, 0.15);
}

.order-form .box-container .summary {
    flex: 1 1 350px;
}

.order-form .box-container .summary h3 {
    color: black;
    font-size: 1.8rem; /* تعديل حجم الخط لعنوان الملخص */
    margin-bottom: 15px;
    text-align: center;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 10px;
    font-weight: bold;
}

.order-form .box-container .summary p {
    font-size: 1.5rem;
    color: red;
    line-height: 1.8;
    margin-bottom: 12px;
    font-weight: bold;
}

.order-form .box-container .summary p span {
    color: black;
    font-weight: bold;
}

/* تنسيق زر تأكيد الطلب عبر واتساب الموجود أسفل الملخص */
.order-form .box-container .summary .btn {
    display: inline-block;
    margin-top: 20px; /* إضافة مساحة أعلى الزر */
    padding: 12px 24px;
    background-color:rgb(240, 26, 26); /* لون واتساب الأخضر */
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 1.6rem;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    width: 100%; /* جعله يأخذ عرض حاوية الملخص */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center; /* توسيط النص داخل الزر */
    border: none; /* إزالة أي حدود افتراضية */
}

.order-form .box-container .summary .btn:hover {
    background-color: #128C7E; /* لون أغمق عند التhover */
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}

#payment-details {
    margin-top: 20px;
    background-color: rgb(11, 9, 9);
    padding: 20px;
    border-radius: 8px;
    color: white;
    font-size: 1.5rem;
    text-align: right;
    border: 1px solid #eee;
}

/* --------------- قسم نبذة عنا - تصميم أنيق --------------- */
.about-us {
    background-color: #f9f9f9; /* لون خلفية رمادي فاتح ودافئ */
    border-radius: 15px; /* حواف مستديرة بشكل أكبر لإضفاء نعومة */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08); /* ظل أكثر بروزًا ولكن ناعم */
    border: 1px solid #e0e0e0; /* حد رمادي فاتح وأنيق */
    padding: 40px; /* حشو داخلي أكبر لتوفير مساحة مريحة للنص */
    margin-bottom: 50px; /* هامش سفلي أكبر لفصله بشكل واضح عن التذييل */
    direction: rtl;
    text-align: center; /* توسيط النص والمحتوى بشكل عام */
    animation: fadeIn 0.8s ease-out forwards; /* إضافة حركة ظهور تدريجي أنيقة */
}

.about-us-heading {
    color: #333; /* لون عنوان أسود داكن وأنيق */
    font-size: 2.5rem; /* حجم خط أكبر للعنوان لجذب الانتباه */
    margin-bottom: 25px;
    border-bottom: 3px solid #e67e22; /* خط سفلي بلون مميز (يمكن تغييره) */
    padding-bottom: 15px;
    font-weight: 700; /* خط أكثر سماكة للعنوان */
    letter-spacing: 0.5px; /* تباعد بسيط بين الأحرف لتحسين المظهر */
}

.about-us-content {
    font-size: 1.6rem; /* حجم خط مريح للقراءة */
    color: #555; /* لون نص رمادي متوسط وأنيق */
    line-height: 2; /* ارتفاع سطر أكبر لتحسين قابلية القراءة */
    margin-bottom: 30px; /* هامش سفلي للنص */
    text-align: center; /* توسيط النص */
}

/* إضافة لمسة جمالية اختيارية */
.about-us::before {
    content: '';
    position: absolute;
    top: 10px;
    right: 10px;
    bottom: 10px;
    left: 10px;
    border-radius: 15px;
    border: 1px dashed #ccc; /* حدود متقطعة اختيارية */
    opacity: 0.6;
    z-index: -1; /* وضعها خلف المحتوى */
}

/* حركة الظهور التدريجي الأنيقة */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* استجابة للشاشات الأصغر */
@media (max-width: 768px) {
    .about-us {
        padding: 30px;
        margin-bottom: 40px;
    }

    .about-us-heading {
        font-size: 2.2rem;
        margin-bottom: 20px;
        padding-bottom: 10px;
    }

    .about-us-content {
        font-size: 1.4rem;
        line-height: 1.8;
    }
}

/* --------------- تذييل الصفحة --------------- */
.footer {
    background-color: #2c3e50;
    color: #ecf0f1;
    text-align: center;
    padding: 30px 0;
    margin-top: 40px;
    box-shadow: 0 -5px 10px rgba(0, 0, 0, 0.1);
}

.footer .share {
    display: flex;
    gap: 30px;
    justify-content: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.footer .share a {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    height: 50px;
    width: 50px;
    line-height: 50px;
    border-radius: 50%;
    font-size: 2rem;
    color: #fff;
    background-color: #e67e22;
    text-decoration: none;
    transition: background-color 0.3s ease, transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease-in-out;
}

.footer .share a:hover {
    background-color: #d35400;
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
}

.footer .credit {
    font-size: 1.2rem;
    color: #bdc3c7;
}

.footer .credit span {
    color: #e67e22;
}

/* --------------- استعلامات الوسائط --------------- */
@media (max-width: 1200px) {
    .container .shopping-cart {
        overflow-x: scroll;
    }

    .container .shopping-cart table {
        width: 2000px;
    }

    .order-form .box-container {
        flex-direction: column; /* جعل العناصر عمودية على الشاشات الأصغر */
    }

    .order-form .box-container form,
    .order-form .box-container .summary {
        width: 100%; /* جعل عرض النموذج والملخص 100% */
        margin-bottom: 20px;
    }
}

@media (max-width: 768px) {
    .header-navigation .navigation-buttons {
        gap: 30px;
    }

    .header-navigation .navigation-buttons a {
        padding: 15px 20px;
        font-size: 1.4rem;
        border-radius: 12px;
    }

    .products .box-container {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 15px;
        padding: 15px;
    }

    .products .box img {
        height: 150px;
        margin-bottom: 10px;
    }

    .products .box .name {
        font-size: 1.6rem;
    }

    .products .box .price {
        font-size: 1.4rem;
        top: 10px;
        left: 10px;
        padding: 3px 10px;
    }

    .order-form .heading {
        font-size: 2.2rem;
    }
}

@media (max-width: 480px) {
    .header-navigation .navigation-buttons {
        gap: 20px;
    }

    .header-navigation .navigation-buttons a {
        padding: 10px 15px;
        font-size: 1.2rem;
        border-radius: 10px;
    }

    .products .box-container {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 10px;
        padding: 10px;
    }

    .products .box img {
        height: 120px;
        margin-bottom: 8px;
    }

    .products .box .name {
        font-size: 1.4rem;
    }

    .products .box .price {
        font-size: 1.2rem;
        top: 8px;
        left: 8px;
        padding: 2px 8px;
    }

    .order-form .heading {
        font-size: 2rem;
    }
}

/* --------------- حركات CSS --------------- */
/* حركة الظهور التدريجي (fadeIn) - قد لا تكون مستخدمة حالياً في نموذج الطلب */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* حركة الانزلاق للأعلى (slideInUp) - مستخدمة في نموذج الطلب */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
 </style>