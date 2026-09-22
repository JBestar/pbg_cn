
    <div class="divTitle"><?= lang('Admin.title_cancel_list') ?></div>
    <div class="divSearch">
        &nbsp;&nbsp;<?= lang('Admin.label_period') ?> :
        <input type="date" id="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
        <input type="date" id="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
        &nbsp;&nbsp;<button type="button" class="btn_search btn_icon" onclick="reqSearch();" title="<?= lang('Admin.btn_search') ?>" aria-label="<?= lang('Admin.btn_search') ?>"><i class="fas fa-search"></i></button>
	</div>

    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_no') ?>.</th>
                    <th><?= lang('Admin.th_round') ?></th>
                    <th><?= lang('Admin.th_uid') ?></th>
                    <th><?= lang('Admin.th_name') ?></th>
                    <th><?= lang('Admin.th_bet_history') ?></th>
                    <th><?= lang('Admin.th_purchase_amount') ?></th>
                    <th><?= lang('Admin.th_purchase_time') ?></th>
                    <th><?= lang('Admin.th_cancel_time') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList"></tbody>
        </table>
	</div>
</div>

<?php if($_ENV['CI_ENVIRONMENT'] == ENV_PRODUCTION) :?>
    <script src="/assets/js/cancel_list.js?v=1"></script>
<?php else :?>
    <script src="/assets/js/cancel_list.js?v=<?=time();?>"></script>
<?php endif ?>
