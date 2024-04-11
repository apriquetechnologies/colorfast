<?php
require('autoload.php');
global $lumise;
if(!empty($_POST) && $_POST['otp'] != ''){
	$otp_result = $lumise->connector->signin($otp = $_POST['otp']);
	if($otp_result){
		header('location:/');
	}else{
		echo $otp_result ; die;
	}
}
include(theme('header.php'));
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
        <h1><?= $lumise->lang('Verify Signin'); ?></h1>
    </div>
</div>
<form action="" method="post" class="form-horizontal" id="checkoutform" accept-charset="utf-8">
	<div class="container">
		<div class="row">
			<div id="checkout" class="padding6 span12">
				<div class="col-md-6 billing col-md-offset-3 container-form text-center">
					<div class="control-group">
						<div class="controls">
							<input name="otp" type="text" maxlength="6" placeholder="Enter email Otp" id="otp" required="">
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<button name="submit" type="submit" class="submit_btn btn btn-large btn-primary" disabled>Submit</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<script>
	$('#otp').on( "keyup", function(){
		if($('#otp').val().length != 6){
			$('.submit_btn').prop('disabled', true);
		}else{
			$('.submit_btn').prop('disabled', false);
		}
	});
</script>
<?php include(theme('footer.php'));?>