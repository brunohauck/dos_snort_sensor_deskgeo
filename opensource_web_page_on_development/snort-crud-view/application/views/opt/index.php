<div class="pull-right">
	<a href="<?php echo site_url('opt/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Sid</th>
		<th>Cid</th>
		<th>Optid</th>
		<th>Opt Proto</th>
		<th>Opt Code</th>
		<th>Opt Len</th>
		<th>Opt Data</th>
		<th>Actions</th>
    </tr>
	<?php foreach($opt as $o){ ?>
    <tr>
		<td><?php echo $o['sid']; ?></td>
		<td><?php echo $o['cid']; ?></td>
		<td><?php echo $o['optid']; ?></td>
		<td><?php echo $o['opt_proto']; ?></td>
		<td><?php echo $o['opt_code']; ?></td>
		<td><?php echo $o['opt_len']; ?></td>
		<td><?php echo $o['opt_data']; ?></td>
		<td>
            <a href="<?php echo site_url('opt/edit/'.$o['sid']); ?>" class="btn btn-info">Edit</a> 
            <a href="<?php echo site_url('opt/remove/'.$o['sid']); ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>