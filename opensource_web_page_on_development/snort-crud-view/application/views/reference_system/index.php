<div class="pull-right">
	<a href="<?php echo site_url('reference_system/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Ref System Id</th>
		<th>Ref System Name</th>
		<th>Actions</th>
    </tr>
	<?php foreach($reference_system as $r){ ?>
    <tr>
		<td><?php echo $r['ref_system_id']; ?></td>
		<td><?php echo $r['ref_system_name']; ?></td>
		<td>
            <a href="<?php echo site_url('reference_system/edit/'.$r['ref_system_id']); ?>" class="btn btn-info">Edit</a> 
            <a href="<?php echo site_url('reference_system/remove/'.$r['ref_system_id']); ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>