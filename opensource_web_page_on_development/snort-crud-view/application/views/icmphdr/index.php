<div class="pull-right">
	<a href="<?php echo site_url('icmphdr/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Sid</th>
		<th>Cid</th>
		<th>Icmp Type</th>
		<th>Icmp Code</th>
		<th>Icmp Csum</th>
		<th>Icmp Id</th>
		<th>Icmp Seq</th>
		<th>Actions</th>
    </tr>
	<?php foreach($icmphdr as $i){ ?>
    <tr>
		<td><?php echo $i['sid']; ?></td>
		<td><?php echo $i['cid']; ?></td>
		<td><?php echo $i['icmp_type']; ?></td>
		<td><?php echo $i['icmp_code']; ?></td>
		<td><?php echo $i['icmp_csum']; ?></td>
		<td><?php echo $i['icmp_id']; ?></td>
		<td><?php echo $i['icmp_seq']; ?></td>
		<td>
            <a href="<?php echo site_url('icmphdr/edit/'.$i['sid']); ?>" class="btn btn-info">Edit</a> 
            <a href="<?php echo site_url('icmphdr/remove/'.$i['sid']); ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>