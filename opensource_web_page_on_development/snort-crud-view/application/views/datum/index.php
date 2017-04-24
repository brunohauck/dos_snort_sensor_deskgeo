<div class="pull-right">
	<a href="<?php echo site_url('datum/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Sid</th>
		<th>Cid</th>
		<th>Data Payload</th>
		<th>Actions</th>
    </tr>
	<?php foreach($data as $d){ ?>
    <tr>
		<td><?php echo $d['sid']; ?></td>
		<td><?php echo $d['cid']; ?></td>
		<td><?php echo $d['data_payload']; ?></td>
		<td>
            <a href="<?php echo site_url('datum/edit/'.$d['sid']); ?>" class="btn btn-info">Edit</a> 
            <a href="<?php echo site_url('datum/remove/'.$d['sid']); ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>