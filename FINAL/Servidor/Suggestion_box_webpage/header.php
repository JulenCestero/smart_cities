<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet" id="bootstrap_header-css">
<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<?php
/* Comprobación de login correcto */
session_start();

if($_SESSION['logged'] != 1){
    session_destroy();
    redirect("login.html"); // Login fallido
}

/* *** FUNCTIONS *** */
function redirect($url) {
  ob_start();
  header('Location: '. $url);
  ob_end_flush();
  die();
}
?>

<div class="container">
    <div class="row">
        <div class="span12">
            <div class="head">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="span6" bgcolor="#FF0000">
                            <h1 class="text-success" style="color:#FFFFFF;">Hogwarts services</h1>
                        </div>

                        <div class="span4 offset2" style="margin-top:15px;">
                            <button class="btn pull-right" type="button" onclick="window.location.href='logout.php'">Logout</button>
                        </div>
                    </div>
                </div>
                <div class="navbar">
                    <div class="navbar-inner">
                        <div class="container">
                            <ul class="nav">
                                <li>
                                    <?php 
                                    if ($_SESSION['privileges'] == 0){
                                        echo('<a href="welcomeWizard.php">Home</a>');
                                    }
                                    else if ($_SESSION['privileges'] == 1){
                                        echo('<a href="welcomeDumbeldore.php">Home</a>');
                                    }
                                    else{
                                        session_destroy();
                                        redirect("login.html");
                                    }
                                    ?>
                                </li>

                                <li>
                                    <?php 
                                    if ($_SESSION['privileges'] == 0){
                                        echo('<a href="suggestions.php">Suggestions</a>');
                                    }
                                    else if ($_SESSION['privileges'] == 1){
                                        echo('<a href="suggestion_reader.php">Suggestions</a>');
                                    }
                                    else{
                                        session_destroy();
                                        redirect("login.html");
                                    }
                                    ?>
                                </li>

                                <li>
                                    <?php 
                                    if ($_SESSION['privileges'] == 0){
                                        //echo('<a href="guides.php">Guías</a>');
                                    }
                                    else if ($_SESSION['privileges'] == 1){
                                        echo('<a href="motion.php">Motion</a>');
                                    }
                                    else{
                                        session_destroy();
                                        redirect("login.html");
                                    }
                                    ?>
                                </li>
                                
                                <li>
                                <?php 
                                    if ($_SESSION['privileges'] == 0){
                                        //echo('<a href="guides.php">Guías</a>');
                                    }
                                    else if ($_SESSION['privileges'] == 1){
                                        echo('<a href="sensores.php">Sensors</a>');
                                    }
                                    else{
                                        session_destroy();
                                        redirect("login.html");
                                    }
                                    ?>
                                </li>

                                <li>
                                    <a href="#">Help</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>

.box_head_align{text-align:center; margin-top:30px;}
.web_design_head{color:#1f86c5; font:normal 48px advent; text-transform:uppercase;}
.blue_head_text{ background:linear-gradient(to top, #fff, #fff, #fff 67%, #1f86c5 67%, #1f86c5, #1f86c5) repeat scroll 0 0 #fff; color:#7d7d7d; font-size:13px; border:1px solid #1f86c5; padding:15px 10px; box-shadow: 5px 5px rgba(193,193,193,.75); min-height:345px; margin-bottom:20px;
    
}
.img_text{font-size:23px; line-height:24px; color:#fff;}
.img_text2{font-size:13px; color:#fff;}
.img_content{text-align:justify; margin-top:10px;}
.img-round{
	margin:0 auto;
	font-size:60px !important;
	padding:25px;
	color:#1f86c5;
	-webkit-border-radius: 54px 55px 55px 54px/54px 54px 55px 55px;
	-moz-border-radius: 54px 55px 55px 54px/54px 54px 55px 55px;
	border-radius: 54px 55px 55px 54px/54px 54px 55px 55px;
	background-color: #fff;
	border: solid 1px #1f86c5;
	}
.div_margin
{
	margin-top:25px;
}
/******************web design box close******/

/******************portfolio slider*************/

.portfolio_bg{text-align:center; margin-top:30px; background:#eeeeee; padding:25px 15px;}
.portfolio_content{color:#7c7c7c;}
.rounded_box{ text-align:center; margin:5px auto;
	/*-webkit-border-radius: 5px; padding:25px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	background-color: rgba(251,174,23,0);
	border: solid 3px #e5e5e5;*/
}
.rouded_text{ font-size:24px; color:#fff; margin-top:20px;}
@media (min-width:768px) and (max-width:1920px) {
.cut_box1{
background-color: #eee;
	background-image: -webkit-linear-gradient(-134deg, #fff 6%, #1277b5 6%, #2388c6);
	background-image: -moz-linear-gradient(-134deg, #fff 6%, #1277b5 6%, #2388c6);
	background-image: -o-linear-gradient(-134deg, #fff 6%, #1277b5 6%, #2388c6);
	background-image: -ms-linear-gradient(-134deg, #fff 6%, #1277b5 6%, #2388c6);
	background-image: linear-gradient(-134deg, #fff 6%, #1277b5 6%, #2388c6);
 color:#7d7d7d; font-size:13px; border:0px solid #1f86c5; padding:15px 10px; min-height:320px; margin-bottom:20px; padding-top:50px; text-align:center;
	
}
.cut_box1 i{color:#fff; border:4px solid #fff; padding:40px; border-radius:12px;}



.cut_box2{ 
background-color: #eee;
	background-image: -webkit-linear-gradient(-134deg, #fff 6%, #3090ca 6%, #3191ca 6%, #45a1d9);
	background-image: -moz-linear-gradient(-134deg, #fff 6%, #3090ca 6%, #3191ca 6%, #45a1d9);
	background-image: -o-linear-gradient(-134deg, #fff 6%, #3090ca 6%, #3191ca 6%, #45a1d9);
	background-image: -ms-linear-gradient(-134deg, #fff 6%, #3090ca 6%, #3191ca 6%, #45a1d9);
	background-image: linear-gradient(-134deg, #fff 6%, #3090ca 6%, #3191ca 6%, #45a1d9);
 color:#7d7d7d; font-size:13px; border:0px solid #1f86c5; padding:15px 10px; min-height:320px; margin-bottom:20px; padding-top:50px; text-align:center;
	
}
.cut_box2 i{color:#fff; border:4px solid #fff; padding:40px; border-radius:12px;}
.cut_box3{ 
background-color: #eee;
	background-image: -webkit-linear-gradient(-134deg, #fff 6%, #5ab4eb 6%, #59b3ea 6%, #46a0d9);
	background-image: -moz-linear-gradient(-134deg, #fff 6%, #5ab4eb 6%, #59b3ea 6%, #46a0d9);
	background-image: -o-linear-gradient(-134deg, #fff 6%, #5ab4eb 6%, #59b3ea 6%, #46a0d9);
	background-image: -ms-linear-gradient(-134deg, #fff 6%, #5ab4eb 6%, #59b3ea 6%, #46a0d9);
	background-image: linear-gradient(-134deg, #fff 6%, #5ab4eb 6%, #59b3ea 6%, #46a0d9);
 color:#7d7d7d; font-size:13px; border:0px solid #1f86c5; padding:15px 10px; min-height:320px; margin-bottom:20px; padding-top:50px; text-align:center;
	
}
.cut_box3 i{color:#fff; border:4px solid #fff; padding:40px; border-radius:12px;}
.cut_box4{ 
background-color: #eee;
	background-image: -webkit-linear-gradient(-134deg, #fff 6%, #59b3ea 6%, #5ab4ea 6%, #72c6f9);
	background-image: -moz-linear-gradient(-134deg, #fff 6%, #59b3ea 6%, #5ab4ea 6%, #72c6f9);
	background-image: -o-linear-gradient(-134deg, #fff 6%, #59b3ea 6%, #5ab4ea 6%, #72c6f9);
	background-image: -ms-linear-gradient(-134deg, #fff 6%, #59b3ea 6%, #5ab4ea 6%, #72c6f9);
	background-image: linear-gradient(-134deg, #fff 6%, #59b3ea 6%, #5ab4ea 6%, #72c6f9);
 color:#7d7d7d; font-size:13px; border:0px solid #1f86c5; padding:15px 10px; min-height:320px; margin-bottom:20px; padding-top:50px; text-align:center;
	
}
.cut_box4 i{color:#fff; border:4px solid #fff; padding:40px; border-radius:12px;}
}
.cut_box_main{ border:1px solid red;}

/******************portfolio slider close*************/




@media (max-width:768px) {
	.request_top{border:none !important; background:none !important; margin-top:15px;}
.navbar-bg{background:#1f86c5; width:100%;}
.navbar-bg a{color:#fff;}
.navbar-bg a:hover{color:#1f86c5;}

.cut_box1a{
background-color: #eee;
	background-image: -webkit-linear-gradient(-134deg, #fff 6%, #1277b5 6%, #2388c6);
	background-image: -moz-linear-gradient(-134deg, #fff 6%, #1277b5 6%, #2388c6);
	background-image: -o-linear-gradient(-134deg, #fff 6%, #1277b5 6%, #2388c6);
	background-image: -ms-linear-gradient(-134deg, #fff 6%, #1277b5 6%, #2388c6);
	background-image: linear-gradient(-134deg, #fff 6%, #1277b5 6%, #2388c6);
 color:#7d7d7d; font-size:13px; border:0px solid #1f86c5; padding:15px 10px; min-height:320px; margin-bottom:20px; padding-top:50px; text-align:center;
	
}
.cut_box1a i{color:#fff; border:4px solid #fff; padding:40px; border-radius:12px;}


.cut_box2a{ 
background-color: #eee;
	background-image: -webkit-linear-gradient(-134deg, #fff 6%, #3090ca 6%, #3191ca 6%, #45a1d9);
	background-image: -moz-linear-gradient(-134deg, #fff 6%, #3090ca 6%, #3191ca 6%, #45a1d9);
	background-image: -o-linear-gradient(-134deg, #fff 6%, #3090ca 6%, #3191ca 6%, #45a1d9);
	background-image: -ms-linear-gradient(-134deg, #fff 6%, #3090ca 6%, #3191ca 6%, #45a1d9);
	background-image: linear-gradient(-134deg, #fff 6%, #3090ca 6%, #3191ca 6%, #45a1d9);
 color:#7d7d7d; font-size:13px; border:0px solid #1f86c5; padding:15px 10px; min-height:320px; margin-bottom:20px; padding-top:50px; text-align:center;
	
}
.cut_box2a i{color:#fff; border:4px solid #fff; padding:40px; border-radius:12px;}
.cut_box3a{ 
background-color: #eee;
	background-image: -webkit-linear-gradient(-134deg, #fff 6%, #5ab4eb 6%, #59b3ea 6%, #46a0d9);
	background-image: -moz-linear-gradient(-134deg, #fff 6%, #5ab4eb 6%, #59b3ea 6%, #46a0d9);
	background-image: -o-linear-gradient(-134deg, #fff 6%, #5ab4eb 6%, #59b3ea 6%, #46a0d9);
	background-image: -ms-linear-gradient(-134deg, #fff 6%, #5ab4eb 6%, #59b3ea 6%, #46a0d9);
	background-image: linear-gradient(-134deg, #fff 6%, #5ab4eb 6%, #59b3ea 6%, #46a0d9);
 color:#7d7d7d; font-size:13px; border:0px solid #1f86c5; padding:15px 10px; min-height:320px; margin-bottom:20px; padding-top:50px; text-align:center;
	
}
.cut_box3a i{color:#fff; border:4px solid #fff; padding:40px; border-radius:12px;}
.cut_box4a{ 
background-color: #eee;
	background-image: -webkit-linear-gradient(-134deg, #fff 6%, #59b3ea 6%, #5ab4ea 6%, #72c6f9);
	background-image: -moz-linear-gradient(-134deg, #fff 6%, #59b3ea 6%, #5ab4ea 6%, #72c6f9);
	background-image: -o-linear-gradient(-134deg, #fff 6%, #59b3ea 6%, #5ab4ea 6%, #72c6f9);
	background-image: -ms-linear-gradient(-134deg, #fff 6%, #59b3ea 6%, #5ab4ea 6%, #72c6f9);
	background-image: linear-gradient(-134deg, #fff 6%, #59b3ea 6%, #5ab4ea 6%, #72c6f9);
 color:#7d7d7d; font-size:13px; border:0px solid #1f86c5; padding:15px 10px; min-height:320px; margin-bottom:20px; padding-top:50px; text-align:center;
	
}
.cut_box4a i{color:#fff; border:4px solid #fff; padding:40px; border-radius:12px;}
}
</style>