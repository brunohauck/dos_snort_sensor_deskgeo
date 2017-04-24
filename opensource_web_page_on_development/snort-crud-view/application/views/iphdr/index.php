<div class="pull-right">
	<a href="<?php echo site_url('iphdr/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Sid</th>
		<th>Cid</th>
		<th>Ip Src</th>
		<th>Ip Dst</th>
		<th>Ip Ver</th>
		<th>Ip Hlen</th>
		<th>Ip Tos</th>
		<th>Ip Len</th>
		<th>Ip Id</th>
		<th>Ip Flags</th>
		<th>Ip Off</th>
		<th>Ip Ttl</th>
		<th>Ip Proto</th>
		<th>Ip Csum</th>
		<th>Actions</th>
    </tr>
	<?php foreach($iphdr as $i){ ?>
    <tr>
		<td><?php echo $i['sid']; ?></td>
		<td><?php echo $i['cid']; ?></td>
		<td><?php echo $i['ip_src']; ?></td>
		<td><?php echo $i['ip_dst']; ?></td>
		<td><?php echo $i['ip_ver']; ?></td>
		<td><?php echo $i['ip_hlen']; ?></td>
		<td><?php echo $i['ip_tos']; ?></td>
		<td><?php echo $i['ip_len']; ?></td>
		<td><?php echo $i['ip_id']; ?></td>
		<td><?php echo $i['ip_flags']; ?></td>
		<td><?php echo $i['ip_off']; ?></td>
		<td><?php echo $i['ip_ttl']; ?></td>
		<td><?php echo $i['ip_proto']; ?></td>
		<td><?php echo $i['ip_csum']; ?></td>
		<td>
            <a href="<?php echo site_url('iphdr/edit/'.$i['sid']); ?>" class="btn btn-info">Edit</a> 
            <a href="<?php echo site_url('iphdr/remove/'.$i['sid']); ?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>