<?php
require('autoload.php');
global $lumise;
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
        <h1><?php echo $lumise->lang('Contact Us'); ?></h1>
    </div>
</div>
<div class="lumise-products" style="padding: 45px 0;">
	<div class="container col-12">
		<div id="success" class="alert alert-success hide"></div>
		<div id="failure" class="alert alert-danger hide"></div>
		<div class="container-form">
			<form action="sendMail.php" id="contact-form">
				<label for="name">Name</label>
				<input type="text" id="name" name="name" placeholder="Enter name.." required>

				<label for="email">Email</label>
				<input type="email" id="email" name="email" placeholder="Email Id.." required>

				<label for="message">Message</label>
				<textarea id="message" name="message" placeholder="Write something.." style="height:200px" required></textarea>

				<input type="submit" value="Submit" class="btn btn-large btn-primary">
			</form>
		</div>
	</div>
</div>
<script>
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
			window.scrollTo({
				top: 100,
				left: 100,
				behavior: "smooth",
			});
			$(successMail).removeClass('hide');
			$(errorMail).addClass('hide');
			$('.contact-form input').val('');
			$('.contact-form textarea').val('');
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