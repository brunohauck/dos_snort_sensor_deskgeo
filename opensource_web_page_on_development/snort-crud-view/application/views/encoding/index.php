<div class="pull-right">
	<a href="<?php echo site_url('encoding/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Encoding Type</th>
		<th>Encoding Text</th>
		<th>Actions</th>
    </tr>
	<?php foreach($encoding as $e){ ?>
    <tr>
		<td><?php echo $e['encoding_type']; ?></td>
		<td><?php echo $e['encoding_text']; ?></td>
		<td>
            <a href="<?php echo site_url('encoding/edit/'.$e['encoding_type']); ?>" class="btn btn-info">Edit</a> 
            <a href="<?php echo site_url('encoding/remove/'.$e['encoding_type']); ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>