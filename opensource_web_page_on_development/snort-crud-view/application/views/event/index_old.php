<div class="pull-right">
	<a href="<?php echo site_url('event/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Sid</th>
		<th>Cid</th>
		<th>Signature</th>
		<th>Timestamp</th>
		<th>Actions</th>
    </tr>
	<?php foreach($event as $e){ ?>
    <tr>
		<td><?php echo $e['sid']; ?></td>
		<td><?php echo $e['cid']; ?></td>
		<td><?php echo $e['signature']; ?></td>
		<td><?php echo $e['timestamp']; ?></td>
		<td>
            <a href="<?php echo site_url('event/edit/'.$e['sid']); ?>" class="btn btn-info">Edit</a> 
            <a href="<?php echo site_url('event/remove/'.$e['sid']); ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>