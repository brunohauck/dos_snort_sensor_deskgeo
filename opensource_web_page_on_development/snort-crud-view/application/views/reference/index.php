<div class="pull-right">
	<a href="<?php echo site_url('reference/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Ref Id</th>
		<th>Ref System Id</th>
		<th>Ref Tag</th>
		<th>Actions</th>
    </tr>
	<?php foreach($reference as $r){ ?>
    <tr>
		<td><?php echo $r['ref_id']; ?></td>
		<td><?php echo $r['ref_system_id']; ?></td>
		<td><?php echo $r['ref_tag']; ?></td>
		<td>
            <a href="<?php echo site_url('reference/edit/'.$r['ref_id']); ?>" class="btn btn-info">Edit</a> 
            <a href="<?php echo site_url('reference/remove/'.$r['ref_id']); ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>