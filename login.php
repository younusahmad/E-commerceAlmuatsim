<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

   $select = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

   if(mysqli_num_rows($select) > 0){
      $row = mysqli_fetch_assoc($select);
      $_SESSION['user_id'] = $row['id'];
      header('location:index.php');
   }else{
      $message[] = 'خطاء في البريد الاكتروني او كلمة المرور!';
   }
   if(isset($message)){
      foreach($message as $message){
          $class = (strpos($message, 'incorrect') !== false) ? 'error' : '';
          echo '<div class="message '.$class.'" onclick="this.remove();">'.$message.'</div>';
      }
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <style>

      body {
    font-family: 'Cairo', sans-serif; /* استخدام خط عربي جميل */
    background: linear-gradient(45deg, #f39c12,rgb(231, 129, 39)); /* تدرج لوني دافئ وجذاب للخلفية */
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    overflow: hidden; /* لمنع ظهور شريط التمرير عند إضافة حركات للخلفية */
}

/* تأثير خلفية متحركة (فقاعات دائرية) */
body::before {
    content: '';
    position: absolute;
    top: -50px;
    left: -50px;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: bubble 8s linear infinite;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
}

body::after {
    content: '';
    position: absolute;
    bottom: -100px;
    right: -100px;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    animation: bubble 12s linear infinite reverse;
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
}

@keyframes bubble {
    0% {
        transform: translate(0, 0) scale(1);
    }
    50% {
        transform: translate(100px, 100px) scale(1.5);
    }
    100% {
        transform: translate(0, 0) scale(1);
    }
}

.form-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    padding: 40px;
    width: 400px;
    text-align: center;
    transform: scale(0.95); /* تأثير ظهور ناعم */
    animation: appear 0.5s ease-out forwards 0.3s;
}

@keyframes appear {
    to {
        transform: scale(1);
        opacity: 1;
    }
    from {
        transform: scale(0.95);
        opacity: 0;
    }
}

.form-container h3 {
    color: #e67e22;
    font-size: 2.5rem;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
}

.form-container .box {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1.6rem;
    text-align: right; /* محاذاة النص لليمين */
    transition: border-color 0.3s ease;
}

.form-container .box:focus {
    border-color: #f39c12;
    outline: none;
    box-shadow: 0 0 5px rgba(243, 156, 18, 0.5);
}

.form-container .btn {
    display: inline-block;
    width: 100%;
    padding: 12px;
    border-radius: 5px;
    background: #e67e22;
    color: #fff;
    font-size: 1.8rem;
    cursor: pointer;
    margin-top: 20px;
    transition: background 0.3s ease, transform 0.2s ease-in-out;
    border: none;
}

.form-container .btn:hover {
    background: #d35400;
    transform: scale(1.02);
}

.form-container p {
    font-size: 1.4rem;
    color: #777;
    margin-top: 15px;
}

.form-container p a {
    color: #f39c12;
    text-decoration: none;
    transition: color 0.3s ease;
}

.form-container p a:hover {
    color: #e67e22;
    text-decoration: underline;
}

.message {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color:rgb(255, 64, 0);
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 1.6rem;
    z-index: 1000;
    cursor: pointer;
    opacity: 0;
    animation: slideIn 0.5s ease-out forwards, fadeOut 0.5s ease-in forwards 3s;
}

@keyframes slideIn {
    to {
        top: 30px;
        opacity: 1;
    }
    from {
        top: 0;
        opacity: 0;
    }
}

@keyframes fadeOut {
    to {
        opacity: 0;
    }
}

.message.error {
    background-color: #c0392b;
}
   </style>
</head>
<body>

<?php


?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>تسجيل الدخول</h3>
      <input type="email" name="email" required placeholder="البريد الالكتروني" class="box">
      <input type="password" name="password" required placeholder="كلمة المرور" class="box">
      <input type="submit" name="submit" class="btn" value="تسجيل الدخول">
      <p>هل تملك حساب بالفعل؟ <a href="register.php"> حساب جديد</a></p>
   </form>

</div>

</body>
</html>