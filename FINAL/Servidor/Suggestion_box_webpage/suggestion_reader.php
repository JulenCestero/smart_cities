<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'header.php';
$conn = new mysqli('localhost', 'admin', 'Admin_Smart-cities4', 'Hogwarts');
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");
$sql = "SELECT * FROM suggestions ORDER BY time DESC";
$result = $conn->query($sql);
if($result->num_rows == 0){
    echo("Error");
}

function create_comment($result){
    while ($fila = $result->fetch_assoc()) {
        $conn2 = new mysqli('localhost', 'admin', 'Admin_Smart-cities4', 'Hogwarts');
        if($conn2->connect_error){
            die("Connection failed: " . $conn2->connect_error);
        }
        $conn2->set_charset("utf8");
        $sql2 = "SELECT name FROM users WHERE id=". $fila["owner"];
        $result2 = $conn2->query($sql2);
        if($result2->num_rows == 0){
            echo("Error");
        }
        $fila2 = $result2->fetch_assoc();
        echo ('<article class="row">
        <div class="col-md-2 col-sm-2 hidden-xs">
          <figure class="thumbnail">
            <img class="img-responsive" src="https://s.tcdn.co/e78/90b/e7890b10-1c4c-3cd4-a176-4e1010807ace/192/1.png" />
            <figcaption class="text-center"></figcaption>
          </figure>
        </div>
        <div class="col-md-10 col-sm-10">
          <div class="panel panel-default arrow left">
            <div class="panel-body">
              <header class="text-left">
                <div class="comment-user"><i class="fa fa-user"></i>'. $fila2["name"] . '</div>
                <time class="comment-date"><i class="fa fa-clock-o"></i>'.$fila["time"].'</time>
              </header>
              <div class="comment-post">
                <p>
                  '. $fila["suggestion"] . '
                </p>
              </div>
              <p class="text-right"><a href="#" class="btn btn-default btn-sm"><i class="fa fa-reply"></i> reply</a></p>
            </div>
          </div>
        </div>
      </article>
      ');
    }
}
?>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<style>
/*font Awesome http://fontawesome.io*/
@import url(//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css);
/*Comment List styles*/
.comment-list .row {
  margin-bottom: 0px;
}
.comment-list .panel .panel-heading {
  padding: 4px 15px;
  position: absolute;
  border:none;
  /*Panel-heading border radius*/
  border-top-right-radius:0px;
  top: 1px;
}
.comment-list .panel .panel-heading.right {
  border-right-width: 0px;
  /*Panel-heading border radius*/
  border-top-left-radius:0px;
  right: 16px;
}
.comment-list .panel .panel-heading .panel-body {
  padding-top: 6px;
}
.comment-list figcaption {
  /*For wrapping text in thumbnail*/
  word-wrap: break-word;
}
/* Portrait tablets and medium desktops */
@media (min-width: 768px) {
  .comment-list .arrow:after, .comment-list .arrow:before {
    content: "";
    position: absolute;
    width: 0;
    height: 0;
    border-style: solid;
    border-color: transparent;
  }
  .comment-list .panel.arrow.left:after, .comment-list .panel.arrow.left:before {
    border-left: 0;
  }
  /*****Left Arrow*****/
  /*Outline effect style*/
  .comment-list .panel.arrow.left:before {
    left: 0px;
    top: 30px;
    /*Use boarder color of panel*/
    border-right-color: inherit;
    border-width: 16px;
  }
  /*Background color effect*/
  .comment-list .panel.arrow.left:after {
    left: 1px;
    top: 31px;
    /*Change for different outline color*/
    border-right-color: #FFFFFF;
    border-width: 15px;
  }
  /*****Right Arrow*****/
  /*Outline effect style*/
  .comment-list .panel.arrow.right:before {
    right: -16px;
    top: 30px;
    /*Use boarder color of panel*/
    border-left-color: inherit;
    border-width: 16px;
  }
  /*Background color effect*/
  .comment-list .panel.arrow.right:after {
    right: -14px;
    top: 31px;
    /*Change for different outline color*/
    border-left-color: #FFFFFF;
    border-width: 15px;
  }
}
.comment-list .comment-post {
  margin-top: 6px;
}
</style>
<body class="clean-body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #1C5FD4">
<div class="container">
  <div class="row" style="background-color: #6895E1">
    <div class="col-md-8">
      <h2 class="page-header">Comments</h2>
        <section class="comment-list">
          <!-- First Comment -->
        <?php
        create_comment($result);
        ?>
        </section>
    </div>
  </div>
</div>
