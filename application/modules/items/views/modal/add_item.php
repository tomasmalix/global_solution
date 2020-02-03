<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title"><?=lang('new_inventory')?></h4>
		</div>
		<?php $attributes = array('class' => 'bs-example form-horizontal','id'=>'itemsAddItem'); echo form_open(base_url().'items/add_item',$attributes); ?>
			<div class="modal-body">
				<div class="form-group">
					<label class="col-lg-4 control-label"><?=lang('inventory_name')?> <span class="text-danger">*</span></label>
					<div class="col-lg-8">
						<input type="text" class="form-control" placeholder="<?=lang('inventory_name')?>" name="item_name">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label"><?=lang('inventory_description')?> <span class="text-danger">*</span></label>
					<div class="col-lg-8">
						<textarea class="form-control ta" name="item_desc" placeholder="<?=lang('inventory_description')?>"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label"><?=lang('quantity')?> <span class="text-danger">*</span></label>
					<div class="col-lg-8">
						<input type="text" class="form-control" placeholder="2" name="quantity">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label"><?=lang('unit_cost')?> <span class="text-danger">*</span></label>
					<div class="col-lg-8">
						<input type="text" class="form-control" placeholder="350.00" name="unit_cost">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label"><?=lang('selling_price')?> <span class="text-danger">*</span></label>
					<div class="col-lg-8">
						<input type="text" class="form-control" placeholder="350.00" name="selling_price">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label"><?=lang('tax_rate')?> <span class="text-danger">*</span></label>
					<div class="col-lg-8">
						<select name="item_tax_rate" class="form-control m-b">
							<option value="0.00"><?=lang('none')?></option>
							<?php foreach ($rates as $key => $tax) { ?>
							<option value="<?=$tax->tax_rate_percent?>"><?=$tax->tax_rate_name?></option>
							<?php } ?>
                        </select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-close" data-dismiss="modal"><?=lang('close')?></a>
				<button type="submit" class="btn btn-success" id="items_add_item"><?=lang('add_inventory')?></button>
			</div>
		</form>
	</div>
</div>