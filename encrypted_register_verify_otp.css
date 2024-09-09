<?php
include_once("../db/conn.php");

//`id`, `full_name`, `email`, `country`, `state`, `city`, `zip_code`, `password`, `user_status`, `login_time` SELECT * FROM `users` WHERE 1
if(isset($_POST['verify']))
{
    $email = $_REQUEST['email'];
    $verify_otp = $_POST['verify_otp'];

        $checkVerifyQuery = "SELECT * FROM admin WHERE email = '$email'";
        $result = $conn->query($checkVerifyQuery);

        if ($result->num_rows > 0) {

            $sql="SELECT * FROM admin WHERE verify_otp='$verify_otp'";
            $query=mysqli_query($conn,$sql);
            if($r=mysqli_fetch_array($query))
            {
                header("location:encrypted_set_password.php?email=$email");
            }
            else{
                    header("location:encrypted_register_verify_otp.php?email=".$email."&msg=Invalide Code");
                }
        } 
        else {
            echo '<script>alert("Email No Did not Match ! Please check your email")</script>';
        }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
<link rel="icon" type="image/png" href="../assets/img/favicon.png"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="admin-source/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="admin-source/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="admin-source/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="admin-source/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="admin-source/vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="admin-source/css/util.css">
	<link rel="stylesheet" type="text/css" href="admin-source/css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic" >
					<img src="admin-source/images/logo.png" width="100%" alt="IMG">
				</div>

				<form action="" method="POST" class="login100-form validate-form">
					<span class="login100-form-title">
						Verify OTP
					</span>

					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100" type="text" name="verify_otp" id="verify_otp" placeholder="Enter OTP">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>


					
					<div class="container-login100-form-btn">
						<button type="submit" class="login100-form-btn" name="verify">
							Verify
						</button>
					</div>

					<!--
					<div class="text-center p-t-12">
						<span class="txt1">
							Forgot
						</span>
						<a class="txt2" href="#">
							Password
						</a>
					</div>
					-->
					
					<div class="text-center p-t-136">
					<!--<a class="txt2" href="sskm-admin-register.php">
							Register Now
							<i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
						</a>
					-->
					</div>
					
				</form>
			</div>
		</div>
	</div>
	
	

	
<!--===============================================================================================-->	
	<script src="admin-source/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="admin-source/vendor/bootstrap/js/popper.js"></script>
	<script src="admin-source/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="admin-source/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="admin-source/vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>