<? include("../php_includes/protectedPage.php"); ?>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {

        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

		var userObj;
		$.ajaxSetup({async:false});
		$.post('../ws/getSchedule', {"sessionId":"<?=$sessionId;?>","grpcode":"testy","month":"09","year":"2012"} , function(data) {
			userObj = data.data;
		});
		var schedule = userObj[0].schedule;
		document.write(JSON.stringify(schedule));
	});
</script>
</head>
<body>

</body>
</html>