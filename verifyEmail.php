<?php
	require('autoload.php');
	global $lumise;
	include(theme('header.php'));
	$checkLogin = null;
	if($_GET['email'] && $_GET['ecode']){
		$checkLogin = $lumise->connector->verify_email($_GET['email'],$_GET['ecode']);
	}
?>
<style>
input, textarea {
  width: 100%;
  padding: 06px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  margin-top: 6px;
  margin-bottom: 16px;
  resize: vertical;
}

input[type=submit] {
  background-color: #04AA6D;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type=submit]:hover {
  background-color: #45a049;
}

.container-form {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}
</style>
<div class="lumise-bread">
    <div class="container">
        <h1><?= $lumise->lang('Verifying Account'); ?></h1>
    </div>
</div>
<form action="https://colorfast.in/register.php" method="post" class="form-horizontal" id="checkoutform" accept-charset="utf-8">
	<div class="container">
		<div class="row">
			<div id="checkout" class="padding6 span12">
				<div class="col-md-6 billing col-md-offset-3 container-form">
				<?php 
					echo "<div class='alert alert-".$checkLogin['status']."'>".$checkLogin['message']."</div>";
				?>
				</div>
			</div>
		</div>
	</div>
</form>
<?php include(theme('footer.php'));?>