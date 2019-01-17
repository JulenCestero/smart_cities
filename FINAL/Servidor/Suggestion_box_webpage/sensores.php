<?php
include 'header.php';
include 'admin_control.php';
?>

<script>
function loadDoc() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("dashboard").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "http://localhost:3000/d/fqAjZzwmk/sensors?orgId=1&from=1547140524706&to=1547155364358", true);
  xhttp.setRequestHeader("Content-type", "application/json");
  xhttp.setRequestHeader("Authorization", "Bearer eyJrIjoiNEd0QVdXVFdNcTJRRDFJMk5yTXFSaVBGVzB2MmFyMDYiLCJuIjoiRHVtYmVsZG9yZSIsImlkIjoxfQ==");
  xhttp.send();
}
</script>
<script>
  function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
  }
</script>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<style>
/*font Awesome http://fontawesome.io*/
@import url(//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css);
/*Comment List styles*/
.column {
  float: left;
  width: 35%;
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
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
<div class="container" style="width:100%; margin: 0 auto;">
  <div class="row">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" id="buttons" method="POST">
      <div class="column">
        <div class="row" style="background-color: #6895E1; padding:5px 15px 15px 15px;">
          <input type="checkbox" name="n_lights[]" value="9"> Master
          <input type="checkbox" name="n_lights[]" value="0"> Slave 0
          <input type="checkbox" name="n_lights[]" value="1"> Slave 1<br>
          <div class="column">
            <h3>Automatic</h3>
              <input type="submit" name="auto" value="Automatic"></input>
          </div>
          <div class="column">
            <h3>Manual</h3>
            <input type="submit" name="light0" value="0 Leds"></input>
            <input type="submit" name="light1" value="1 Led"></input>
            <input type="submit" name="light2" value="2 Leds"></input>
            <input type="submit" name="light3" value="3 Leds"></input>
          </div>
        </div>
      </div>
    </form>
    <div class="column">
      <iframe src="http://192.168.4.118:3000/d/fqAjZzwmk/sensors?orgId=1&from=1547555709527&to=1547577309527&kiosk" frameborder="0" scrolling="no"  height="80%" width="200%" >
    <!-- <iframe id="dashboard" frameborder="0" scrolling="no"  height="80%" width="80%"" onload="loadDoc(); " /> -->

    </div>
  </div> 
  <?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
  if(!empty($_POST['n_lights'])){
    $n_lights = $_POST['n_lights'];
    $n = count($n_lights);
    $conn = new mysqli('localhost', 'admin', 'Admin_Smart-cities4', 'Hogwarts');
    if($conn->connect_error){
      die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    for($ii = 0; $ii < $n; $ii++){
      $sensor_id = $n_lights[$ii];
      if(isset($_POST['auto'])) {
        $sql = "UPDATE sensor_status SET mode = 0 WHERE sensor_status.mote = " .  $sensor_id;
      }else if(isset($_POST['light0'])){
        $sql = "UPDATE sensor_status SET mode = 1, n_leds = 0 WHERE sensor_status.mote = " .  $sensor_id;
      }else if (isset($_POST['light1'])){
        $sql = "UPDATE sensor_status SET mode = 1, n_leds = 1 WHERE sensor_status.mote = " .  $sensor_id;
      }else if (isset($_POST['light2'])){
        $sql = "UPDATE sensor_status SET mode = 1, n_leds = 2 WHERE sensor_status.mote = " .  $sensor_id;
      }else if (isset($_POST['light3'])){
        $sql = "UPDATE sensor_status SET mode = 1, n_leds = 3 WHERE sensor_status.mote = " .  $sensor_id;
      }else{
        session_destroy();
        redirect("login.html");
      }
      if ($conn->query($sql) === TRUE){
        echo('Success!'); // Success
      } else {
        echo("Error: " . $sql . "<br>" . $conn->error);
      }
    }
  }
  
}
function IsChecked($chkname,$value)
    {
        if(!empty($_POST[$chkname]))
        {
            foreach($_POST[$chkname] as $chkval)
            {
                if($chkval == $value)
                {
                    return true;
                }
            }
        }
        return false;
    }

?>
</div>

<!-- <script type="text/javascript">
  $.ajax(
    {
      type: 'GET',
      url: 'http://localhost:3000/d/fqAjZzwmk/sensors?orgId=1&from=1547140524706&to=1547155364358',
      contentType: 'application/json',
      beforeSend: function(xhr, settings) {
        xhr.setRequestHeader(
          'Authorization', 'Basic ' + window.btoa('admin:Smart-cities4')
        );
      },
      success: function(data) {
        alert('Caca');
        $('#dashboard').attr('src', 'http://localhost:3000/d/fqAjZzwmk/sensors?orgId=1&from=1547140524706&to=1547155364358');
        $('#dashboard').contents().find('html').html(data);
      }
      error: function(data){
        alert('Caca');
      }
    }
  );
</script> -->
