

<script src="./js/jquery.min.js"></script>
<script src="./js/jquery.form.js"></script>
<script>
$(document).on('change', '#image_upload_file', function () {


$('#image_upload_form').ajaxForm({

    success: function(html, statusText, xhr, $form) {
		obj = $.parseJSON(html);
		if(obj.status){
			$("#imgArea>img").prop('src',obj.image_medium);
		}else{
			alert(obj.error);
		}
    },

}).submit();

});
</script>

<div id="imgContainer">
  <form enctype="multipart/form-data" action="image_submit.php" method="post" name="image_upload_form" id="image_upload_form">
    <div id="imgArea"><img src="./img/default.jpg">

      <div id="imgChange">
        <input type="file"  style="color:transparent;" onchange="this.style.color = 'black' accept="image/*" name="image_upload_file" id="image_upload_file">
      </div>
    </div>
  </form>
</div>
