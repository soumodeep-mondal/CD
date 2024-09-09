<?php
include_once("db/conn.php");
include_once("db/email_authenticate.php");
include_once("db/system_ip_tracking.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';


/*
    //`user_id`, `membership_status`, `membership_category`, `membership_id`, `user_track_url`, `name`, `age`, `contact_no`, `profile_picture`, 
    //`city`, `state`, `country`, `zip_code`, `address`, `qualification`, `have_skill_in`, `expert_in_skill`, `mother_language`, `fluent_language`, 
    //`ethinity`, `gender`, `maretial_status`, `family_members_no`, `monthly_gross_family_income`, `occupation`, `id_proof_documents_name`,
    //`id_proof_documents_image`, `documents_upload_status`, `profile_completion`, `email`, `password`, `dob`, `date_of_joining`, `account_status`, 
    //`verify_otp`, `login_date`, `login_time`, `logout_time` SELECT * FROM `users` WHERE 1
 */
$signup_id="";
if(isset($_POST['register']))
{
    // Get input values from the login form
    $signup_id=$_REQUEST['ref'];
    $membership_status= ""; //  NUL Defualt Not Change 
    $membership_category= "FREE MEMBER"; // Defualt Not Change 
    $create_datetime=date("YmdHis");
    $membership_id= $create_datetime;// Genarate Number action
    $documents_upload_status="0";//  Defualt Not Change 

    // Resgister user Data
    $name= $_POST['name'] ;
    $city= $_POST['city'] ;
    $state= $_POST['state'] ;
    $country= $_POST['country'] ;
    $zip_code= $_POST['zip_code'] ;
    $email= $_POST['email'] ;
    $password= $_POST['password'] ;
    $dob = (new DateTime($_POST['dob']))->format('Y-m-d');
    $account_status="0";
    $date_of_joining=date("Y-m-d");
    $agree= $_POST['agree'] ;
    $genarate_otp= random_int(100000, 999999);

    $profile_completion=12;
    if($name!="")
    {
        $profile_completion+=4;
    }
    if($city!="")
    {
        $profile_completion+=4;
    }
    if($state!="")
    {
        $profile_completion+=4;
    }
    if($country!="")
    {
        $profile_completion+=4;
    }
    if($zip_code!="")
    {
        $profile_completion+=4;
    }
    if($email!="")
    {
        $profile_completion+=4;
    }
    if($dob!="")
    {
        $profile_completion+=4;
    }



        // Check if the email is already registered
        $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($checkEmailQuery);

        if ($result->num_rows > 0) {
            echo '<script>alert("Email already registered. Please use a different email address.")</script>';
        } 
		else {
            // Hash the password (you can use other encryption methods)
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            /*
            `user_id`, `membership_category`, `membership_id`, `name`, `age`, `contact_no`, `profile_picture`, 
            `city`, `state`, `country`, `zip_code`, `address`, `qualification`, `have_skill_in`, `expert_in_skill`, 
            `mother_language`, `fluent_language`, `ethinity`, `gender`, `maretial_status`, `family_members_no`, 
            `monthly_gross_family_income`, `occupation`, `id_proof_documents_name`, `id_proof_documents_image`, 
            `documents_upload_status`, `membership_status`, `profile_completion`, `email`, `password`, `dob`, 
            `date_of_joining`, `account_status`, `verify_otp`, `login_date`, `login_time`, `logout_time`
            SELECT * FROM `users` WHERE 1
            */

            //`gift_id`, `gift_name`, `gift_value`, `no_of_sites` SELECT * FROM `gift_master` WHERE 1
 

            $insertQuery = "INSERT INTO users (membership_status, membership_category, membership_id, documents_upload_status, profile_completion, name, city, state, country, zip_code, email, password, dob, date_of_joining, account_status, verify_otp, agree,signup_id)
                            VALUES ('$membership_status', '$membership_category', '$membership_id', '$documents_upload_status', '$profile_completion', '$name', '$city', '$state', '$country', '$zip_code', '$email', '$hashedPassword', '$dob', '$date_of_joining', '$account_status', '$genarate_otp', '$agree', '$signup_id')";

            $query=mysqli_query($conn,$insertQuery);
            $last_user_id=mysqli_insert_id($conn);




            if($query){

                /* ==================================Bonus Send=============================================================== */
                //`signup_bonus_id`, `signup_bonus_name`, `signup_bonus_value`, `signup_bonus_site_distrubration` SELECT * FROM `signup_bonus_master` WHERE 1
                $sql1="SELECT * FROM signup_bonus_master WHERE signup_bonus_id=1";
                $query1=mysqli_query($conn,$sql1);
                if($prd=mysqli_fetch_array($query1)) 
                {
                    $signup_bonus_name=$prd['signup_bonus_name'];
                    $signup_bonus_value=$prd['signup_bonus_value'];
                    $signup_bonus_site_distrubration=$prd['signup_bonus_site_distrubration'];
                    $min_affilites=round($signup_bonus_value*0.75/$signup_bonus_site_distrubration);
                    $max_affilites=round($signup_bonus_value*1.25/$signup_bonus_site_distrubration);
                    $start_date = date('Y-m-d');
                    $end_date = date('Y-m-d', strtotime($start_date . ' +15 days'));
                }

                //`id`, `user_id`, `bonus_name`, `bonus_value`, `site_distrubration`, `min_affilites`, `max_affilites`, `start_date`, `end_date` SELECT * FROM `affiliates_master` WHERE 1
                $insert_welcome_bonus=mysqli_query($conn,"INSERT INTO affiliates_master (user_id, bonus_name, bonus_value, site_distrubration, min_affilites, max_affilites, start_date, end_date)
                VALUES ('$last_user_id', '$signup_bonus_name','$signup_bonus_value','$signup_bonus_site_distrubration','$min_affilites','$max_affilites','$start_date','$end_date')");

                /* ==================================Bonus Send=============================================================== */

                /* ==================================Notification Create=============================================================== */

                $sql2="SELECT * FROM notification_master WHERE notification_category='WELCOME'";
                $query2=mysqli_query($conn,$sql2);
                if($prd2=mysqli_fetch_array($query2)) 
                {
                    $notification_subject=$prd2['notification_subject'];
                    $notification_message=nl2br($prd2['notification_message']);

                    $create_message="Hi ".$name.",<br><br>".$notification_message ;    //$name connect to register data

                    date_default_timezone_set('Asia/Kolkata');
                    $notification_date=date("Y-m-d");
                    $notification_time=date("H:i");
                }
                //SELECT `id`, `user_id`, `subject`, `message`, `notification_time` FROM `create_notification` WHERE 1
                $insert_notification=mysqli_query($conn,"INSERT INTO create_notification (user_id, `subject`, `message`, notification_date, notification_time)
                VALUES ('$last_user_id', '$notification_subject','$create_message','$notification_date','$notification_time')");
               
                    if($insert_notification)
                    {
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
                        $mail->Subject = $notification_subject ;
                        $mail->Body = $create_message;
                        $mail->send();
                  
                    }


                 /* ==================================Notification Create=============================================================== */






                if($insert_welcome_bonus && $insert_notification){

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
                    $mail->Subject = " Send a confirmation code" ;
                    $mail->Body = "<br> Your Confirmation Code is: </b>".$genarate_otp;
                  
                  
                    if($mail->send()){
                      
                        header("Location:user_verify_otp.php?email=".$email);
                    }
                    else{
                      echo "<script>alert('Error please try again')</script>";
                    }
                
                }
                else {
                    echo "Error: " . $insert_welcome_bonus . "<br>" . $conn->error;
                }

            }
            else {
                echo "Error: " . $insertQuery . "<br>" . $conn->error;
            }




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

</head>
<body>



<!-- ==== Header === -->
<?php include("website_header.php");?>
<!-- ==== Header === -->




<div class="container">
   
    <div class="row">
        <div class="col-md-12 pt-3 pb-3">
            <div class="col-lg-12 mt-4 mb-4 p-4" style="position:relative; left:50%; transform: translate(-50%, 0); background-color: #fff;box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px; border-radius: 5px;">
                <header><h3 class="">Sign Up</h3></header>
                <!--
                //`user_id`, `name`, `street`, `city`, `state`, `country`, `zip_code`, `contact_no`, `membership_category`, `membership_id`, 
                // `email`, `password`, `dob`, `date_of_joining`, `account_status`, `verify_otp`, `login_time` SELECT * FROM `users` WHERE 1
                -->
                <form action="" method="post">
                    <div class="row">


                        <div class="col-md-4 pb-2">
                            <label for="form-create-account-full-name">Full Name</label>
                            <input type="text" class="form-control" name="name"  required>
                        </div>
                        <div class="col-md-4">
                            <label for="form-create-account-email">Email</label>
                            <input type="email" class="form-control" name="email"  required>
                        </div>     
                        <div class="col-lg-4 form-group">
                            <label for="form-create-account-email">Date Of Birth</label>
                            <input type="date" class="form-control" name="dob"  required>
                        </div> 
                        <div class="col-lg-3 form-group">
                            <label for="form-create-account-email">Country</label>
                            <select  class="form-control" name="country" id="country" required>
                                <option value="" selected> Choose</option>
                                    <option value="Afganistan">Afganistan</option>
                                    <option value="Albania">Albania</option>
                                    <option value="Algeria">Algeria</option>
                                    <option value="Andorra">Andorra</option>
                                    <option value="Angola">Angola</option>
                                    <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                    <option value="Argentina">Argentina</option>
                                    <option value="Armenia">Armenia</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Austria">Austria</option>
                                    <option value="Azerbaijan">Azerbaijan</option>
                                    <option value="Bahamas">Bahamas</option>
                                    <option value="Bahrain">Bahrain</option>
                                    <option value="Bangladesh">Bangladesh</option>
                                    <option value="Barbados">Barbados</option>
                                    <option value="Belarus">Belarus</option>
                                    <option value="Belgium">Belgium</option>
                                    <option value="Belize">Belize</option>
                                    <option value="Benin">Benin</option>
                                    <option value="Bhutan">Bhutan</option>
                                    <option value="Bolivia">Bolivia</option>
                                    <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                    <option value="Botswana">Botswana</option>
                                    <option value="Brazil">Brazil</option>
                                    <option value="Brunei">Brunei</option>
                                    <option value="Bulgaria">Bulgaria</option>
                                    <option value="Burkina Faso">Burkina Faso</option>
                                    <option value="Burundi">Burundi</option>
                                    <option value="Cambodia">Cambodia</option>
                                    <option value="Cameroon">Cameroon</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Cape Verde">Cape Verde</option>
                                    <option value="Central African Republic">Central African Republic</option>
                                    <option value="Chad">Chad</option>
                                    <option value="Chile">Chile</option>
                                    <option value="China">China</option>
                                    <option value="Colombia">Colombia</option>
                                    <option value="Comoros">Comoros</option>
                                    <option value="Republic of the Congo">Republic of the Congo</option>
                                    <option value="Democratic and Republic of  Congo">Democratic and Republic of  Congo</option>
                                    <option value="Costa Rica">Costa Rica</option>
                                    <option value="Croatia">Croatia</option>
                                    <option value="Cote D' Ivoire">Cote D' Ivoire</option>
                                    <option value="Cuba">Cuba</option>
                                    <option value="Cyprus">Cyprus</option>
                                    <option value="Cook Island">Cook Island</option>
                                    <option value="Czech Republic">Czech Republic</option>
                                    <option value="Denmark">Denmark</option>
                                    <option value="Djibouti">Djibouti</option>
                                    <option value="Dominica">Dominica</option>
                                    <option value="Dominican Republic">Dominican Republic</option>
                                    <option value="Ecuador">Ecuador</option>
                                    <option value="Egypt">Egypt</option>
                                    <option value="El Salvador">El Salvador</option>
                                    <option value="Equatorial Guinea">Equatorial Guinea</option>
                                    <option value="Eritrea">Eritrea</option>
                                    <option value="Estonia">Estonia</option>
                                    <option value="Eswatini">Eswatini</option>
                                    <option value="Ethiopia">Ethiopia</option>
                                    <option value="Fiji">Fiji</option>
                                    <option value="Finland">Finland</option>
                                    <option value="France">France</option>
                                    <option value="Gabon">Gabon</option>
                                    <option value="Gambia">Gambia</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Germany">Germany</option>
                                    <option value="Ghana">Ghana</option>
                                    <option value="Gibraltar">Gibraltar</option>
                                    <option value="Greece">Greece</option>
                                    <option value="Greenland">Greenland</option>
                                    <option value="Grenada">Grenada</option>
                                    <option value="Guam">Guam</option>
                                    <option value="Guatemala">Guatemala</option>
                                    <option value="Guinea-Bissau">Guinea-Bissau</option>
                                    <option value="Guyana">Guyana</option>
                                    <option value="Haiti">Haiti</option>
                                    <option value="Honduras">Honduras</option>
                                    <option value="Hong Kong">Hong Kong</option>
                                    <option value="Hungary">Hungary</option>
                                    <option value="Iceland">Iceland</option>
                                    <option value="India">India</option>
                                    <option value="Indonesia">Indonesia</option>
                                    <option value="Iran">Iran</option>
                                    <option value="Iraq">Iraq</option>
                                    <option value="Ireland">Ireland</option>
                                    <option value="Israel">Israel</option>
                                    <option value="Italy">Italy</option>
                                    <option value="Jamaica">Jamaica</option>
                                    <option value="Japan">Japan</option>
                                    <option value="Jordan">Jordan</option>
                                    <option value="Kazakhstan">Kazakhstan</option>
                                    <option value="Kenya">Kenya</option>
                                    <option value="Kiribati">Kiribati</option>
                                    <option value="Korea North">Korea North</option>
                                    <option value="Korea South">Korea South</option>
                                    <option value="Kuwait">Kuwait</option>
                                    <option value="Kyrgyzstan">Kyrgyzstan</option>
                                    <option value="Kosovo">Kosovo</option>
                                    <option value="Laos">Laos</option>
                                    <option value="Latvia">Latvia</option>
                                    <option value="Lebanon">Lebanon</option>
                                    <option value="Lesotho">Lesotho</option>
                                    <option value="Liberia">Liberia</option>
                                    <option value="Libya">Libya</option>
                                    <option value="Liechtenstein">Liechtenstein</option>
                                    <option value="Lithuania">Lithuania</option>
                                    <option value="Luxembourg">Luxembourg</option>
                                    <option value="Macau">Macau</option>
                                    <option value="Madagascar">Madagascar</option>
                                    <option value="Malawi">Malawi</option>
                                    <option value="Malaysia">Malaysia</option>
                                    <option value="Maldives">Maldives</option>
                                    <option value="Mali">Mali</option>
                                    <option value="Malta">Malta</option>
                                    <option value="Marshall Islands">Marshall Islands</option>
                                    <option value="Mauritania">Mauritania</option>
                                    <option value="Mauritius">Mauritius</option>
                                    <option value="Mexico">Mexico</option>
                                    <option value="Micronesia">Micronesia</option>
                                    <option value="Moldova">Moldova</option>
                                    <option value="Monaco">Monaco</option>
                                    <option value="Mongolia">Mongolia</option>
                                    <option value="Montenegro">Montenegro</option>
                                    <option value="Macedonia">Macedonia</option>
                                    <option value="Morocco">Morocco</option>
                                    <option value="Mozambique">Mozambique</option>
                                    <option value="Myanmar">Myanmar</option>
                                    <option value="Namibia">Namibia</option>
                                    <option value="Nauru">Nauru</option>
                                    <option value="Nepal">Nepal</option>
                                    <option value="Netherlands">Netherlands</option>
                                    <option value="New Zealand">New Zealand</option>
                                    <option value="Nicaragua">Nicaragua</option>
                                    <option value="Niger">Niger</option>
                                    <option value="Nigeria">Nigeria</option>
                                    <option value="Norway">Norway</option>
                                    <option value="Oman">Oman</option>
                                    <option value="Pakistan">Pakistan</option>
                                    <option value="Palau">Palau</option>
                                    <option value="Palestine">Palestine</option>
                                    <option value="Panama">Panama</option>
                                    <option value="Papua New Guinea">Papua New Guinea</option>
                                    <option value="Paraguay">Paraguay</option>
                                    <option value="Peru">Peru</option>
                                    <option value="Philippines">Philippines</option>
                                    <option value="Poland">Poland</option>
                                    <option value="Portugal">Portugal</option>
                                    <option value="Puerto Rico">Puerto Rico</option>
                                    <option value="Qatar">Qatar</option>
                                    <option value="Romania">Romania</option>
                                    <option value="Russia">Russia</option>
                                    <option value="Rwanda">Rwanda</option>
                                    <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                                    <option value="Saint Lucia">Saint Lucia</option>
                                    <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                                    <option value="Samoa">Samoa</option>
                                    <option value="San Marino">San Marino</option>
                                    <option value="São Tomé and Príncipe">São Tomé and Príncipe</option>
                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                    <option value="Senegal">Senegal</option>
                                    <option value="Serbia">Serbia</option>
                                    <option value="Seychelles">Seychelles</option>
                                    <option value="Sierra Leone">Sierra Leone</option>
                                    <option value="Singapore">Singapore</option>
                                    <option value="Slovakia">Slovakia</option>
                                    <option value="Slovenia">Slovenia</option>
                                    <option value="Solomon Islands">Solomon Islands</option>
                                    <option value="Somalia">Somalia</option>
                                    <option value="South Africa">South Africa</option>
                                    <option value="South Sudan">South Sudan</option>
                                    <option value="Spain">Spain</option>
                                    <option value="Sri Lanka">Sri Lanka</option>
                                    <option value="Sudan">Sudan</option>
                                    <option value="Suriname">Suriname</option>
                                    <option value="Sweden">Sweden</option>
                                    <option value="Switzerland">Switzerland</option>
                                    <option value="Syria">Syria</option>
                                    <option value="Taiwan">Taiwan</option>
                                    <option value="Tajikistan">Tajikistan</option>
                                    <option value="Tanzania">Tanzania</option>
                                    <option value="Thailand">Thailand</option>
                                    <option value="Timor-Leste ">Timor-Leste </option>
                                    <option value="Togo">Togo</option>
                                    <option value="Tonga">Tonga</option>
                                    <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                    <option value="Tunisia">Tunisia</option>
                                    <option value="Turkey">Turkey</option>
                                    <option value="Turkmenistan">Turkmenistan</option>
                                    <option value="Tuvalu">Tuvalu</option>
                                    <option value="Uganda">Uganda</option>
                                    <option value="Ukraine">Ukraine</option>
                                    <option value="United Arab Emirates">United Arab Emirates</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="United States">United States</option>
                                    <option value="Uruguay">Uruguay</option>
                                    <option value="Uzbekistan">Uzbekistan</option>
                                    <option value="Vanuatu">Vanuatu</option>
                                    <option value="Vatican City">Vatican City</option>
                                    <option value="Venezuela">Venezuela</option>
                                    <option value="Vietnam">Vietnam</option>
                                    <option value="Yemen">Yemen</option>
                                    <option value="Zambia">Zambia</option>
                                    <option value="Zimbabwe">Zimbabwe</option>
                                    <option value="Other" id="other">Other</option>



                            </select>
                        </div>  
                        <div class="col-lg-3 form-group">
                            <label for="form-create-account-email">State</label>
                            <input type="text" class="form-control" name="state"  required>
                        </div>    
                        <div class="col-lg-3 form-group">
                            <label for="form-create-account-email">City</label>
                            <input type="text" class="form-control" name="city"  required>
                        </div>       
                        <div class="col-lg-3 form-group">
                            <label for="form-create-account-email">Zip code</label>
                            <input type="number" class="form-control"  name="zip_code"  required>
                        </div>   
                        <div class="col-lg-6 form-group">
                            <label for="form-create-account-password">Password</label>
                            <input type="password" class="form-control" name="password" id="password" minlength="8" maxlength="15"   required>
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="form-create-account-confirm-password">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password"  minlength="8" maxlength="15"   required>
                        </div>
                        <div class="col-lg-12 form-group">
                            <input type="checkbox"  name="agree" value="I agree" required>
                            <label for="">I Agree With <a href="" class="text-primary">Terms & Conditions and Privacy Policy </a></label>
                        </div>
                    </div>


                    <div class="form-group clearfix">
                        <button type="submit" class="btn btn-sm btn-theme" name="register" style="position: relative;">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.row -->
</div>


<!-- ==== Footer === -->
<?php include("website_footer.php");?>
<!-- ==== Footer === -->


<!-- External JS libraries -->
<script src="assets/js/jquery-2.2.0.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
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

<script>
            var password = document.getElementById("password"),
            confirm_password = document.getElementById("confirm_password");

            function validatePassword() {
            if (password.value != confirm_password.value) {
                confirm_password.setCustomValidity("Passwords Don't Match");
            } else {
                confirm_password.setCustomValidity("");
            }
            }

            password.onchange = validatePassword;
            confirm_password.onkeyup = validatePassword;
    </script>
</body>

</html>