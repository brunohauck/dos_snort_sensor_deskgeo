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
        <h2 style="margin-top:0px">Event Read</h2>
        <table class="table">
	    <tr><td>Signature</td><td><?php echo $signature; ?></td></tr>
	    <tr><td>Timestamp</td><td><?php echo $timestamp; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('event_snort') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
        </body>
</html>