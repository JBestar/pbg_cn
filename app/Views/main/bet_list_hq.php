
    <div class="divTitle"><?= lang('Admin.menu_bet_list') ?>
        <span id="spanBetModeLabel" style="font-size:14px;font-weight:normal;margin-left:12px;color:#555;"></span>
    </div>
    <div class="divSearch">
        <span id="spanBackWrap" style="display:none;margin-right:10px;">
            <button type="button" class="btn_red" onclick="backToAgencyList();"><?= lang('Admin.btn_back') ?></button>
        </span>
        &nbsp;&nbsp;<?= lang('Admin.label_period') ?> :
        <input type="date" id="inputDateS" name="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
        <input type="date" id="inputDateE" name="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
        &nbsp;&nbsp;&nbsp;<?= lang('Admin.label_uid') ?> :
        <input type="text" id="inputUserID" name="inputUserID" class="inputDate" value="">
        &nbsp;&nbsp;<button type="button" class="btn_search btn_icon" onclick="reqSearch();" title="<?= lang('Admin.btn_search') ?>" aria-label="<?= lang('Admin.btn_search') ?>"><i class="fas fa-search"></i></button>
	</div>

    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_role') ?></th>
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
}
</style>

<script>
window.ADMIN_I18N = window.ADMIN_I18N || {};
window.ADMIN_I18N.th_detail_view = <?= json_encode(lang('Admin.th_detail_view'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.role_agency = <?= json_encode(lang('Admin.role_agency'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.role_store = <?= json_encode(lang('Admin.role_store'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.label_agency_bets = <?= json_encode(lang('Admin.label_agency_bets'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.label_store_bets = <?= json_encode(lang('Admin.label_store_bets'), JSON_UNESCAPED_UNICODE) ?>;
</script>
<?php if($_ENV['CI_ENVIRONMENT'] == ENV_PRODUCTION) :?>
    <script src="/assets/js/bet_list_hq.js?v=1"></script>
<?php else :?>
    <script src="/assets/js/bet_list_hq.js?v=<?=time();?>"></script>
<?php endif ?>
