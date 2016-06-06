	<div id="footer">
		Copyright &copy; <?php echo $siteName;?> by <?php echo $siteAuthor;?>. (<?php echo $copyright_year;?>) | Current Timezone: <?php echo date_default_timezone_get(); ?>
	</div>
</div>

<script src="js/jquery-2.2.0.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script> -->
<script>
$(function() {
    $('#ajaxform .input_submit').click(function(ev) {
    	$('#warningwords').html('');
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
					console.log('success ajax');
					console.log(data);
					if (data['error'] !== undefined) {
						$('#warningwords').html('Err: ' + data['error']);
					} else if (eOnPage.length == 0) {
						var options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' };
						$('#conflist').prepend("<a href='" + data['pu'] + "' target='_blank'><li class='left'><span class='confbrev'>" + cb + "</span><span class='fullname'>" + data['fn'] + "</span><span class='currentnumber'>" + data['pc'] + "</span><span class='deadlines'>Registration Due: " + (new Date(parseInt(data['reg']) * 1000)).toLocaleString('en-US', options) + "</span><span class='deadlines'>Submission Due: " + (new Date(parseInt(data['sub']) * 1000)).toLocaleString('en-US', options) + "</span><span class='lastupdate'>Updated " + data['lu'] + "</span></li></a>");
					} else {
						// console.log(eOnPage.children("span[class='currentnumber']").html());
						eOnPage.children("span[class='currentnumber']").html(data['pc']);
						// console.log(eOnPage.children("span[class='lastupdate']").html());
						eOnPage.children("span[class='lastupdate']").html("Updated " + data['lu']);
					}
				}
			});
	    }
    });
});
</script>

</body>
