	<div id="footer">
		Copyright &copy; <?php echo $siteName;?> by <?php echo $siteAuthor;?>. (<?php echo $copyright_year;?>)
	</div>
</div>

<script src="js/jquery-2.2.0.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script> -->
<script>
$(function() {
    $('#ajaxform .input_submit').click(function(ev) {
    	if($("#ajaxform")[0].checkValidity()) {
	    	ev.preventDefault();
	    	var cb = $("#ajaxform .input_text[name='cb']").val(),
	    	fn = $("#ajaxform .input_text[name='fn']").val(),
	    	pu = $("#ajaxform .input_text[name='pu']").val();
	    	$.ajax ({ 
				url: 'ajax.php',
				data: {"cb": cb, "fn": fn, "pu": pu},
				dataType: "json",
				type: 'post',
				success: function(data) {
					var eOnPage = $("#conflist a li:contains('"+cb+"')");
					if (eOnPage.length == 0) {
						if (data['error'].length) {
							alert(data['error']);
						} else {
							$('#conflist').prepend("<a href='" + pu + "' target='_blank'><li class='left'><span class='confbrev'>" + cb + "</span><span class='currentnumber'>" + data['papercount'] + "</span><span class='lastupdate'>" + data['lastupdate'] + "</span><span class='fullname'>" + fn + "</span></li></a>");
						}
					} else {
						// console.log(eOnPage.children("span[class='currentnumber']").html());
						eOnPage.children("span[class='currentnumber']").html(data['papercount']);
						// console.log(eOnPage.children("span[class='lastupdate']").html());
						eOnPage.children("span[class='lastupdate']").html(data['lastupdate']);
					}
				}
			});
	    }
    });
});
</script>

</body>
