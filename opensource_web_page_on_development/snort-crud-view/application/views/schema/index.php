<div class="pull-right">
	<a href="<?php echo site_url('schema/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Actions</th>
    </tr>
	<?php foreach($schema as $s){ ?>
    <tr>
		<td>
            <a href="<?php echo site_url('schema/edit/'.$s['']); ?>" class="btn btn-info">Edit</a> 
            <a href="<?php echo site_url('schema/remove/'.$s['']); ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>