
    <div class="divTitle"><?= lang('Admin.menu_agency_ce') ?></div>
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
                    <?php if (!empty($show_grade)) { ?>
                    <th><?= lang('Admin.th_level') ?></th>
                    <?php } ?>
                    <th><?= lang('Admin.th_uid') ?></th>
                    <th><?= lang('Admin.th_name') ?></th>
                    <?php if (!empty($show_grade)) { ?>
                    <th><?= lang('Admin.th_charge_request') ?></th>
                    <th><?= lang('Admin.th_direct_charge') ?></th>
                    <th><?= lang('Admin.th_exchange_request') ?></th>
                    <th><?= lang('Admin.th_recover') ?></th>
                    <?php } else { ?>
                    <th><?= lang('Admin.th_charge_amount') ?></th>
                    <th><?= lang('Admin.th_exchange_recover') ?></th>
                    <?php } ?>
                    <th><?= lang('Admin.th_point_change_amt') ?></th>
                    <th><?= lang('Admin.th_diff') ?></th>
                    <th><?= lang('Admin.th_history_detail') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList"></tbody>
        </table>
	</div>
</div>

<style>
.btn-detail-view {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 8px;
    background: #1a73e8;
    color: #fff !important;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    line-height: 1.3;
}
.btn-detail-view:hover { background: #1557b0; color: #fff !important; }
.btn-ce-confirm {
    display: inline-block;
    margin-left: 6px;
    padding: 4px 12px;
    border-radius: 6px;
    background: #2e7d32;
    color: #fff !important;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    line-height: 1.3;
    vertical-align: middle;
}
.btn-ce-confirm:hover { background: #1b5e20; color: #fff !important; }
.td-pending-req { line-height: 1.4; vertical-align: middle; white-space: nowrap; }
.ce-req-amt { vertical-align: middle; }
.td-diff-pos { color: #0000fe; }
.td-diff-neg { color: #fe0000; }
</style>

<script>
window.ADMIN_I18N = window.ADMIN_I18N || {};
window.ADMIN_I18N.th_detail_view = <?= json_encode(lang('Admin.th_detail_view'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.th_total = <?= json_encode(lang('Admin.th_total'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.role_agency = <?= json_encode(lang('Admin.role_agency'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_ok = <?= json_encode(lang('Admin.btn_ok'), JSON_UNESCAPED_UNICODE) ?>;
window.CE_SCOPE = 'agency';
window.CE_DETAIL_PATH = '/Main/agency_ce_detail';
window.CE_API = '/api/agency_ce_summary';
window.CE_SHOW_GRADE = <?= !empty($show_grade) ? 'true' : 'false' ?>;
window.CE_SHOW_PENDING = <?= !empty($show_grade) ? 'true' : 'false' ?>;
</script>
<?php if($_ENV['CI_ENVIRONMENT'] == ENV_PRODUCTION) :?>
    <script src="/assets/js/ce_list.js?v=5"></script>
<?php else :?>
    <script src="/assets/js/ce_list.js?v=<?=time();?>"></script>
<?php endif ?>
