
    <div class="divTitle"><?= lang('Admin.menu_store_ce') ?></div>
    <div class="divSearch">
        &nbsp;&nbsp;<?= lang('Admin.label_period') ?> :
        <input type="date" id="inputDateS" value="<?php echo date('Y-m-d', strtotime('-6 months')); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
        <input type="date" id="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
        &nbsp;&nbsp;&nbsp;<?= lang('Admin.label_uid') ?> :
        <input type="text" id="inputUserID" class="inputDate" value="">
        &nbsp;&nbsp;<input type="submit" onclick="reqSearch();" class="btn_search" value="<?= lang('Admin.btn_search') ?>">
        <button class="btn_red" onclick="location.reload();"><?= lang('Admin.btn_refresh') ?></button>
	</div>

    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>
                    <?php if (!empty($show_agency)) { ?>
                    <th><?= lang('Admin.role_agency') ?></th>
                    <th><?= lang('Admin.role_store') ?></th>
                    <?php } else { ?>
                    <th><?= lang('Admin.th_uid') ?></th>
                    <?php } ?>
                    <th><?= lang('Admin.th_name') ?></th>
                    <th><?= lang('Admin.th_charge_amount') ?></th>
                    <th><?= lang('Admin.th_exchange_recover') ?></th>
                    <th><?= lang('Admin.th_point_convert_amt') ?></th>
                    <th><?= lang('Admin.th_diff') ?></th>
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
}
.td-diff-pos { color: #0000fe; }
.td-diff-neg { color: #fe0000; }
</style>

<script>
window.ADMIN_I18N = window.ADMIN_I18N || {};
window.ADMIN_I18N.th_detail_view = <?= json_encode(lang('Admin.th_detail_view'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.th_total = <?= json_encode(lang('Admin.th_total'), JSON_UNESCAPED_UNICODE) ?>;
window.CE_SCOPE = 'store';
window.CE_DETAIL_PATH = '/Main/store_ce_detail';
window.CE_API = '/api/store_ce_summary';
window.CE_SHOW_GRADE = false;
window.CE_SHOW_AGENCY = <?= !empty($show_agency) ? 'true' : 'false' ?>;
</script>
<?php if($_ENV['CI_ENVIRONMENT'] == ENV_PRODUCTION) :?>
    <script src="/assets/js/ce_list.js?v=3"></script>
<?php else :?>
    <script src="/assets/js/ce_list.js?v=<?=time();?>"></script>
<?php endif ?>
