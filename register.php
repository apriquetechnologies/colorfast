<?php
require('autoload.php');
global $lumise;
include(theme('header.php'));
$register_result = null;
if(!empty($_POST)){
	$register_result = $lumise->connector->register();
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
        <h1><?= $lumise->lang('Create Account'); ?></h1>
    </div>
</div>
<form action="https://colorfast.in/register.php" method="post" class="form-horizontal" id="checkoutform" accept-charset="utf-8">
	<div class="container">
		<div class="row">
			<div id="checkout" class="padding6 span12">
				<div class="col-md-6 billing col-md-offset-3 container-form">
				<?php 
					if($register_result && gettype($register_result) === 'string'){
						echo '<div id="success" class="alert alert-success">Account created successfully. Please <a href="/signin.php">login</a></div>';
						unset($_POST);
					}else{
					if($register_result && gettype($register_result) === 'array'){
						echo '<div class="alert alert-danger">'.$register_result['error'].'</div>'; } ?>
					<div class="control-group span6">
						<label for="first_name" class="control-label">First Name <em>*</em></label>
						<div class="controls">
							<input name="first_name" type="text" placeholder="" id="first_name" value="<?= isset($_POST['first_name']) ? $_POST['first_name']:''; ?>" required="">
						</div>
					</div>
					<div class="control-group span6 last">
						<label for="last_name" class="control-label">Last Name<em>*</em></label>
						<div class="controls">
							<input name="last_name" type="text" placeholder="" id="last_name" value="<?= isset($_POST['last_name'])?$_POST['last_name']:''; ?>" required="">
						</div>
					</div>
					<div class="control-group">
						<label for="email" class="control-label">E-Mail<em>*</em></label>
						<div class="controls">
							<input name="email" type="email" id="email" value="<?= isset($_POST['email'])?$_POST['email']:''; ?>" required="">
						</div>
					</div>
					<div class="control-group span6">
						<label for="password" class="control-label">Password <em>*</em></label>
						<div class="controls">
							<input name="password" type="password" placeholder="" id="password" required="">
						</div>
					</div>
					<div class="control-group span6 last">
						<label for="confirm_password" class="control-label">Confirm Password<em>*</em></label>
						<div class="controls">
							<input name="confirm_password" type="password" placeholder="" id="confirm_password" required="">
						</div>
					</div>
					<div class="control-group">
						<label for="address" class="control-label">Street Address<em>*</em></label>
						<div class="controls">
							<input name="address" placeholder="229 Broadway" type="text" id="address" value="<?= isset($_POST['address'])?$_POST['address']:''; ?>" required="">
						</div>
					</div>
					<div class="control-group span4">
						<label for="zip" class="control-label">Zip Code<em>*</em></label>
						<div class="controls">
							<input name="zip" type="text" id="zip" value="<?= isset($_POST['zip'])?$_POST['zip']:''; ?>" required="">
						</div>
					</div>
					<div class="control-group span4">
						<label for="city" class="control-label">City<em>*</em></label>
						<div class="controls">
							<input name="city" type="text" placeholder="New York" id="city" value="<?= isset($_POST['city'])?$_POST['city']:''; ?>" required="">
						</div>
					</div>
					<div class="control-group span4 last">
						<label for="phone" class="control-label">Phone<em>*</em></label>
						<div class="controls">
							<input name="phone" type="text" id="phone" value="<?= isset($_POST['phone'])?$_POST['phone']:''; ?>" required="">
						</div>
					</div>
					<div class="control-group span6">
						<label for="submit" class="control-label"></label>
						<div class="controls">
							<button name="submit" type="submit" class="submit_btn btn btn-large btn-primary" disabled>Register</button>
						</div>
					</div>
				<?php	} ?>
				</div>
			</div>
		</div>
	</div>
</form>
<script>
	$('#confirm_password').on( "keyup", function(){
		if($('#password').val() != $('#confirm_password').val()){
			$('button').prop('disabled', true);
		}else{
			$('.submit_btn').prop('disabled', false);
		}
	});
    $("#contact-form").submit(function (event) {
        var successMail = '#success' ;
        var errorMail = '#error' ;

        event.preventDefault();

        var formData = $("#contact-form").serialize();

        $("#contact-form :input").prop("disabled", true);

        $.ajax({
            type: 'POST',
            url: $('#contact-form').attr('action'),
            data: formData
        })
		.done(function (response) {
			console.log(response);
			$(successMail).removeClass('hide');
			$(errorMail).addClass('hide');
			$('.contact-form input').val('');
			$('.contact-form textarea').val('');
			console.log(response);
			$(successMail).text(response.success);
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			console.log(jqXHR, textStatus, errorThrown);
			var msg = JSON.parse(jqXHR.responseText) ;
			$(errorMail).removeClass('hide');
			$(successMail).addClass('hide');
			$("#contact-form :input").prop("disabled", false);
			$(errorMail).text(msg.error);
		});


    })
</script>
<?php include(theme('footer.php'));?>