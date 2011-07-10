<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>PHPUnit - Butler</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/dark-hive/jquery-ui.css" />
<link href='http://fonts.googleapis.com/css?family=Play:regular,bold' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Droid+Sans+Mono&v2' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="css/butler.css" />    

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>
<script type="text/javascript">
    var first_run = false;
    var doing_test = false;
    var current = '';
   
    function runTest(test){
        if(doing_test == true){
            return;
        }
        
        doing_test = true;
        $("#input-test").addClass("ui-autocomplete-loading");
        
        $.ajax({
          url: 'run.php',
          data: {test: test},
          success: function(data){
              $("#input-test").removeClass("ui-autocomplete-loading");
              doing_test = false;
              
              if(data){
                first_run = true;
                $("#result").html(data);
              }
          }
        })
        
    }
   
	$(function() {

		$( "#input-test" ).autocomplete({
			minLength: 1,
			source: function(request, response) {
				$.ajax({
					url: "search.php",
					dataType: "json",
					data: {q: request.term},
					success: function( data ) {
						response( data );
					}
				});
			},
            select: function(event,ui){
                $(this).attr('value',ui.item.label);
                $(this).autocomplete('close');
                event.stopImmediatePropagation();
                runTest(ui.item.value);
            }
		});
        
        
        $("#input-test").keyup(function(event){
             if(event.keyCode == 13 && $("#input-test").attr('value')){ //Enter
                runTest($("#input-test").attr('value'));
             }
            
            if(!$("#input-test").attr('value')){
                $(this).autocomplete('search','[latest]');
            }
        });
        
        $("#formRun").submit(function(event){
            event.preventDefault();
        })
        
        setInterval(function(){
            if(first_run == true && $('#auto_reload').attr('checked') == true){
                runTest('[reload]');
            }
        },<?php echo BUTLER_RELOAD_INTERVAL ?>);
        
        $("#auto_reload").click(function(){
            $.cookie('reload',$("#auto_reload").attr('checked'));
        });
        
        if($.cookie('reload') == 'true'){
            $("#auto_reload").attr('checked',true);
        }
        
        <?php if(empty($error_msg)): ?>
        $("#input-test").autocomplete('search','[latest]');
        $("#input-test").focus();
        <?php endif; ?>
	});
</script>
</head>
<body>
<div id="body">
    <h1>
        <div class="phpunit">PHPUnit</div><div class="butler">Butler</div>
    </h1>
    <div id="actions">
        <input type="checkbox" id="auto_reload" value="1" />
        <label for="auto_reload">Auto reload</label>
    </div>
    <form name="formRun" id="formRun"  id="form-run">
    <div class="ui-widget">
        <input type="text" id="input-test" name="run" autocomplete="off" value="<?php echo $run; ?>" />
    </div>
    </form>

    <?php if(!empty($error_msg)): ?>
    <?php foreach($error_msg as $msg): ?>
    <div class="error_wrap">
     <div class="icon error">
        <div class="symbol">!</div>
    </div>
    <span><?php echo $msg; ?></span>
    </div>
    <?php endforeach; ?>
    <div class="error_desc">
    bootstrap.php contains the settings you need to change before using PHPUnit Butler.    
    </div>
    <?php endif; ?>
    
    <div id="result"></div>
</div>
</body>
</html>