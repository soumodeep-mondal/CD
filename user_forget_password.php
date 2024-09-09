<?php
include_once("db/conn.php");
include_once("db/email_authenticate.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
date_default_timezone_set('Asia/Kolkata');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $token = bin2hex(random_bytes(50)); // Generate a random token
    $expiration = date('Y-m-d H:i:s', strtotime('+1 hour')); // Set token expiration time
    //$resetLink = $_SERVER['HTTP_HOST']."/reset_password.php?token=$token";

    $local_folder="192.168.0.101/skc@soumo/software_websites_2024/COMPLETE%20SOFTWARE/Link%20Profit%20Latest/Link%20Profit%20Portal/";
    $resetLink = $local_folder."reset_password.php?token=$token";



    // Update user record with reset token and expiration
    $stmt = $conn->prepare("UPDATE users SET reset_token=?, reset_token_expiration=? WHERE email=?");
    $stmt->bind_param('sss', $token, $expiration, $email);
    $stmt->execute();

    // Send reset link via email using PHPMailer
    $mail = new PHPMailer(true);
    
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = $authenticate_host; // attached to head require_once("../db/email_authenticate.php");
        $mail->SMTPAuth = $authenticate_SMTPAuth; 
        $mail->Username = $authenticate_username; 
        $mail->Password = $authenticate_password; 
        $mail->SMTPSecure = $authenticate_SMTPSecure; 
        $mail->Port = $authenticate_Port; 

        //Recipients
        $mail->setFrom($authenticate_form_email, $authenticate_form_name); 
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "Click the following link to reset your password: <a href='$resetLink'>$resetLink</a>";
        $mail->AltBody = "Click the following link to reset your password: $resetLink";

        $mail->send();
        $msg= "A password reset link has been sent to your email.";
    } catch (Exception $e) {
        $msg= "Message could not be sent. Mailer Error:".$mail->ErrorInfo;
    }
    header("location:user_forget_password.php?msg=$msg");
}


?>
<!DOCTYPE html>
<html lang="eng">

<head>
    <title>Earnify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="assets/css/magnific-popup.css">
    <link type="text/css" rel="stylesheet" href="assets/css/jquery.selectBox.css">
    <link type="text/css" rel="stylesheet" href="assets/css/dropzone.css">
    <link type="text/css" rel="stylesheet" href="assets/css/rangeslider.css">
    <link type="text/css" rel="stylesheet" href="assets/css/animate.min.css">
    <link type="text/css" rel="stylesheet" href="assets/css/leaflet.css">
    <link type="text/css" rel="stylesheet" href="assets/css/slick.css">
    <link type="text/css" rel="stylesheet" href="assets/css/slick-theme.css">
    <link type="text/css" rel="stylesheet" href="assets/css/slick-theme.css">
    <link type="text/css" rel="stylesheet" href="assets/css/map.css">
    <link type="text/css" rel="stylesheet" href="assets/css/jquery.mCustomScrollbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link type="text/css" rel="stylesheet" href="assets/fonts/font-awesome/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="assets/fonts/flaticon/font/flaticon.css">


    <!-- Favicon icon -->
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon" >

    <!-- Google fonts -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800%7CPlayfair+Display:400,700%7CRoboto:100,300,400,400i,500,700">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" id="style_sheet" href="assets/css/skins/default.css">
   <script language="JavaScript">
    function validate()
    {
        var email=document.getElementById("email").value;
        var password=document.getElementById("password").value;

        if(email=="")
        {
            alert("Email cannot be blank");
            document.getElementById("email").focus();
            return false;

        }
        if(password=="")
        {
            alert("Password cannot be blank");
            document.getElementById("password").focus();
            return false;

        }
        return true;
    }
    </script>

</head>
<body>


<!-- ==== Header === -->
<?php include("website_header.php");?>
<!-- ==== Header === -->



<div class="container pb-2 pt-2">
    <div class="row">
        <div class="col-md-12">
            <div class="col-lg-5 mt-5 mb-5 p-4" style="position:relative; left:50%; transform: translate(-50%, 0); background-color: #fff;box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px; border-radius: 5px;">
               <h3 class="text-center">Forget Password</h3>

                <form action="" method="post">
                    <div class="form-group">
                        <label for="form-create-account-email">Email</label>
                        <input type="email" class="form-control" name="email"  id="email" placeholder="Enter Your Email Id" required>
                    </div>
                    <p class="text-danger text-center"><b><?php if (isset($_REQUEST["msg"])) {$msg = $_REQUEST["msg"];echo $msg ;}?></b></p>

                    <div class="form-group clearfix">
                        <button type="submit" class="btn btn-sm btn-theme w-100" name="login" style="position: relative; float: right;">Send Reset Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- ==== Footer === -->
<?php include("website_footer.php");?>
<!-- ==== Footer === -->


<!-- External JS libraries -->
<script src="assets/js/jquery-2.2.0.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.selectBox.js"></script>
<script src="assets/js/rangeslider.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script src="assets/js/jquery.filterizr.js"></script>
<script src="assets/js/wow.min.js"></script>
<script src="assets/js/backstretch.js"></script>
<script src="assets/js/jquery.countdown.js"></script>
<script src="assets/js/jquery.scrollUp.js"></script>
<script src="assets/js/particles.min.js"></script>
<script src="assets/js/typed.min.js"></script>
<script src="assets/js/dropzone.js"></script>
<script src="assets/js/jquery.mb.YTPlayer.js"></script>
<script src="assets/js/leaflet.js"></script>
<script src="assets/js/leaflet-providers.js"></script>
<script src="assets/js/leaflet.markercluster.js"></script>
<script src="assets/js/slick.min.js"></script>
<script src="assets/js/maps.js"></script>
<script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD4omYJlOaP814WDcCG8eubXcbhB-44Uac"></script>
<script src="assets/js/ie-emulation-modes-warning.js"></script>
<!-- Custom JS Script -->
<script  src="assets/js/app.js"></script>
</body>

</html>