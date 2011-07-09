<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>PHPUnit - Butler</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/jquery.ui.all.css" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/dark-hive/jquery-ui.css" />
<link href='http://fonts.googleapis.com/css?family=Play:regular,bold' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans+Mono&v2' rel='stylesheet' type='text/css'>

<link rel="stylesheet" type="text/css" href="assets/stylesheets/new.css" />    
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.3/jquery-ui.min.js"></script>
<script type="text/javascript">
	$(function() {
		var cache = {};
		$( "#input-test" ).autocomplete({
			minLength: 1,
			source: function(request, response) {
				if ( request.term in cache ) {
					response( cache[ request.term ] );
					return;
				}

				$.ajax({
					url: "search.php",
					dataType: "json",
					data: request,
					success: function( data ) {
						cache[ request.term ] = data;
						response( data );
					}
				});
			}
		});

        $("#btn-run-test").button()
            .click(function(){
            $("#input-test").addClass("ui-autocomplete-loading");

                $("#result").load('runtest.php?test='+$('#input-test')[0].value, function(){
                    $("#input-test").removeClass("ui-autocomplete-loading");
                });
            });
                                  

	});
</script>
</head>
<body>
<div id="body">
    <h1><div class="phpunit">PHPUnit</div><div class="frontend">Butler</div></h1>
    <form method="GET" name="formRun"  id="form-run">
    <div class="ui-widget">
        <input type="text" id="input-test" name="run" autocomplete="off" value="<?php echo $run; ?>" />
        <input id="btn-run-test" type="button" value="Run Test" />
    </div>
    </form>

    <div id="result"></div>
</div>
</body>
</html>