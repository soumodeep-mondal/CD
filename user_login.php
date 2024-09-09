<?php
include_once("db/conn.php");
include_once("db/email_authenticate.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if(isset($_POST['login']))
{
    //     //`account_status`, `verify_otp`, `login_date`, `login_time`, `logout_time` SELECT * FROM `users` WHERE 1

    $email= $_POST['email'] ;
    $password= $_POST['password'] ;
    $genarate_otp= random_int(100000, 999999);
    // Set the default timezone to Kolkata
    date_default_timezone_set('Asia/Kolkata');
    $LOG_DATE=date('Y-m-d');
    $LOG_TIME = date("h:i A");





                // Validate input (you can add more validation as per your requirements)
                
                    $sql = "SELECT * FROM `users` WHERE email = '$email'";
                    $query=mysqli_query($conn,$sql);

                    if($fetch=mysqli_fetch_array($query)){

                        $user_id=$fetch['user_id'];
                        $membership_id=$fetch['membership_id'];
                        $contact_no=$fetch['contact_no'];
                        $account_status=$fetch['account_status'];
                        $server_password=$fetch['password'];
                       

                            if($account_status*1==1){
                              
                                // Verify the password start
                                if (password_verify($password, $server_password)) {
                                    
                                    
                                    session_start();
                                    $SESSION_ID=session_id();
                                    $_SESSION['session_id']=$SESSION_ID;
                                    $_SESSION['email']=$email;
                                    $_SESSION['user_id']=$user_id;
                                    $_SESSION['membership_id']=$membership_id;
                                    $_SESSION['contact_no']=$contact_no;


                                    // Collecting For Activities Log
                                    //`id`, `session_id`, `user_id`, `log_date`, `log_time`, `logout_time`, `user_ip`, `activities` SELECT * FROM `user_activity_log` WHERE 1
                                    
                                    
                                    $USER_IP=$_SERVER['REMOTE_ADDR'];
                                    $ACTIVITIES='lOGIN PORTAL , ';
                                    mysqli_query($conn,"INSERT INTO users_log_history (session_id, user_id, login_date, login_time ) VALUES ('$SESSION_ID', '$user_id', '$LOG_DATE', '$LOG_TIME')"); // Login History data
                                    mysqli_query($conn,"INSERT INTO user_activity_log (session_id, user_id, log_date, log_time, user_ip, activities)VALUES('$SESSION_ID', '$user_id', '$LOG_DATE', '$LOG_TIME',  '$USER_IP', '$ACTIVITIES')"); // Login History data
                                    // Collecting For Activities Log


                    
                                    header("location:user_dashboard/dashboard.php");
                                }
                                else {
                                    header("location:user_login.php?msg=Invalide user-password");
                                }
                                // Verify the password End

                            }
                            else{
                                $update_verify_otp=mysqli_query($conn,"UPDATE users set verify_otp=$genarate_otp where email='$email'");

                                if($update_verify_otp){
                                    $mail = new PHPMailer(true);
                                    $mail->isSMTP();
									$mail->Host = $authenticate_host; // attached to head require_once("../db/email_authenticate.php");
									$mail->SMTPAuth = $authenticate_SMTPAuth; 
									$mail->Username = $authenticate_username; 
									$mail->Password = $authenticate_password; 
									$mail->SMTPSecure = $authenticate_SMTPSecure; 
									$mail->Port = $authenticate_Port; 
									$mail->setFrom($authenticate_form_email, $authenticate_form_name); 
                                    $mail->addAddress($email);
                                    $mail->isHTML(true);
                                    $mail->Subject = "Earnify Send a confirmation code" ;
                                    $mail->Body = "<br> Your Confirmation Code is: </b>".$genarate_otp;
                                    if($mail->send()){
                                    
                                        header("location:user_verify_otp.php?email=$email");
                                    }
                                    else{
                                    echo "<script>alert('Error please try again')</script>";
                                    }
                                }

                            }                            

                    } 
                    else {
                        header("location:user_login.php?msg=Invalide user-email");
                    }

                

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
            <div class="col-lg-5 mt-4 mb-4 p-4" style="position:relative; left:50%; transform: translate(-50%, 0); background-color: #fff;box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px; border-radius: 5px;">
               <h3 class="text-center">Login</h3>

                <form action="" method="post" onSubmit="return validate();">
                    <div class="form-group">
                        <label for="form-create-account-email">Email</label>
                        <input type="email" class="form-control" name="email"  id="email" placeholder="Enter Your Email Id" required>
                    </div>
                    <div class="form-group">
                        <label for="form-create-account-password">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password"  required>
                    </div>
                    <?php if (isset($_REQUEST["msg"])) { ?>
                    <p class="text-danger text-center"><b><?php $msg = $_REQUEST["msg"]; echo $msg ;?></b></p>
                    <?php } ?>
                    <div>
                        <a href="user_forget_password.php" class="text-primary">Forget Password</a>
                    </div>
                    
                    <div class="form-group clearfix mt-3">
                        <button type="submit" class="btn btn-sm btn-theme w-100" name="login" style="position: relative; float: right;">Submit</button>
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