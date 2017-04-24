<!doctype html>
<html>
    <head>
        <title>harviacode.com - codeigniter crud generator</title>
        <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>"/>
        <style>
            body{
                padding: 15px;
            }
        </style>
    </head>
    <body>
        <h2 style="margin-top:0px">Event <?php echo $button ?></h2>
        <form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
            <label for="int">Signature <?php echo form_error('signature') ?></label>
            <input type="text" class="form-control" name="signature" id="signature" placeholder="Signature" value="<?php echo $signature; ?>" />
        </div>
	    <div class="form-group">
            <label for="datetime">Timestamp <?php echo form_error('timestamp') ?></label>
            <input type="text" class="form-control" name="timestamp" id="timestamp" placeholder="Timestamp" value="<?php echo $timestamp; ?>" />
        </div>
	    <input type="hidden" name="sid" value="<?php echo $sid; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('event_snort') ?>" class="btn btn-default">Cancel</a>
	</form>
    </body>
</html>