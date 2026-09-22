
    <div class="divTitle"><?= lang('Admin.menu_bet_list') ?></div>
    <div class="divSearch">
    &nbsp;&nbsp;<?= lang('Admin.label_period') ?> :
        <input type="date" id="inputDateS" name="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
        <input type="date" id="inputDateE" name="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
        &nbsp;&nbsp;&nbsp;<?= lang('Admin.label_uid') ?> :
        <input type="text" id="inputUserID" name="inputUserID" class="inputDate" value="">
        &nbsp;&nbsp;<input type="submit" onclick="reqSearch();" class="btn_search" value="<?= lang('Admin.btn_search') ?>">
	</div>

    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_uid') ?></th>
                    <th><?= lang('Admin.th_name') ?></th>
                    <th><?= lang('Admin.th_hold_money') ?></th>
                    <th><?= lang('Admin.th_purchase_amount') ?></th>
                    <th><?= lang('Admin.th_hit_amount') ?></th>
                    <th><?= lang('Admin.th_diff') ?></th>
                    <th><?= lang('Admin.th_hit_rounds') ?></th>
                    <th><?= lang('Admin.th_point') ?></th>
                    <th><?= lang('Admin.th_agen_point') ?></th>
                    <th><?= lang('Admin.th_detail_view') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList"></tbody>
        </table>
	</div>
</div>

<style>
.btn-detail-view {
    padding: 6px 14px;
    font-size: 13px;
    line-height: 1.3;
    text-decoration: none;
}
</style>

<?php if($_ENV['CI_ENVIRONMENT'] == ENV_PRODUCTION) :?>
    <script src="/assets/js/bet_list.js?v=3"></script>
<?php else :?>
    <script src="/assets/js/bet_list.js?v=<?=time();?>"></script>
<?php endif ?>
