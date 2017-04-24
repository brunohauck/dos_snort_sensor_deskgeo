<div class="pull-right">
	<a href="<?php echo site_url('detail/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Detail Type</th>
		<th>Detail Text</th>
		<th>Actions</th>
    </tr>
	<?php foreach($detail as $d){ ?>
    <tr>
		<td><?php echo $d['detail_type']; ?></td>
		<td><?php echo $d['detail_text']; ?></td>
		<td>
            <a href="<?php echo site_url('detail/edit/'.$d['detail_type']); ?>" class="btn btn-info">Edit</a> 
            <a href="<?php echo site_url('detail/remove/'.$d['detail_type']); ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>