
<?php echo form_open('iphdr/add',array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="ip_src" class="col-md-4 control-label">Ip Src</label>
		<div class="col-md-8">
			<input type="text" name="ip_src" value="<?php echo $this->input->post('ip_src'); ?>" class="form-control" id="ip_src" />
		</div>
	</div>
	<div class="form-group">
		<label for="ip_dst" class="col-md-4 control-label">Ip Dst</label>
		<div class="col-md-8">
			<input type="text" name="ip_dst" value="<?php echo $this->input->post('ip_dst'); ?>" class="form-control" id="ip_dst" />
		</div>
	</div>
	<div class="form-group">
		<label for="ip_ver" class="col-md-4 control-label">Ip Ver</label>
		<div class="col-md-8">
			<input type="text" name="ip_ver" value="<?php echo $this->input->post('ip_ver'); ?>" class="form-control" id="ip_ver" />
		</div>
	</div>
	<div class="form-group">
		<label for="ip_hlen" class="col-md-4 control-label">Ip Hlen</label>
		<div class="col-md-8">
			<input type="text" name="ip_hlen" value="<?php echo $this->input->post('ip_hlen'); ?>" class="form-control" id="ip_hlen" />
		</div>
	</div>
	<div class="form-group">
		<label for="ip_tos" class="col-md-4 control-label">Ip Tos</label>
		<div class="col-md-8">
			<input type="text" name="ip_tos" value="<?php echo $this->input->post('ip_tos'); ?>" class="form-control" id="ip_tos" />
		</div>
	</div>
	<div class="form-group">
		<label for="ip_len" class="col-md-4 control-label">Ip Len</label>
		<div class="col-md-8">
			<input type="text" name="ip_len" value="<?php echo $this->input->post('ip_len'); ?>" class="form-control" id="ip_len" />
		</div>
	</div>
	<div class="form-group">
		<label for="ip_id" class="col-md-4 control-label">Ip Id</label>
		<div class="col-md-8">
			<input type="text" name="ip_id" value="<?php echo $this->input->post('ip_id'); ?>" class="form-control" id="ip_id" />
		</div>
	</div>
	<div class="form-group">
		<label for="ip_flags" class="col-md-4 control-label">Ip Flags</label>
		<div class="col-md-8">
			<input type="text" name="ip_flags" value="<?php echo $this->input->post('ip_flags'); ?>" class="form-control" id="ip_flags" />
		</div>
	</div>
	<div class="form-group">
		<label for="ip_off" class="col-md-4 control-label">Ip Off</label>
		<div class="col-md-8">
			<input type="text" name="ip_off" value="<?php echo $this->input->post('ip_off'); ?>" class="form-control" id="ip_off" />
		</div>
	</div>
	<div class="form-group">
		<label for="ip_ttl" class="col-md-4 control-label">Ip Ttl</label>
		<div class="col-md-8">
			<input type="text" name="ip_ttl" value="<?php echo $this->input->post('ip_ttl'); ?>" class="form-control" id="ip_ttl" />
		</div>
	</div>
	<div class="form-group">
		<label for="ip_proto" class="col-md-4 control-label">Ip Proto</label>
		<div class="col-md-8">
			<input type="text" name="ip_proto" value="<?php echo $this->input->post('ip_proto'); ?>" class="form-control" id="ip_proto" />
		</div>
	</div>
	<div class="form-group">
		<label for="ip_csum" class="col-md-4 control-label">Ip Csum</label>
		<div class="col-md-8">
			<input type="text" name="ip_csum" value="<?php echo $this->input->post('ip_csum'); ?>" class="form-control" id="ip_csum" />
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>

<?php echo form_close(); ?>