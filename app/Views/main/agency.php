
    <div class="divTitle"><?= lang('Admin.title_agency_mgmt') ?></div>
    <div class="divSearch">
			<input type="date" id="inputDateS" name="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
            <input type="date" id="inputDateE" name="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
			&nbsp;&nbsp;&nbsp;<?= lang('Admin.label_uid') ?> : <input type="text" id="inputSubId" name="inputSubId" class="inputDate" value="" autocomplete="off">
			<input type="submit" class="btn_search" onclick="reqSearch();" value="<?= lang('Admin.btn_search') ?>">&nbsp;
            <button type="button" class="btn_blue" style="float:right; margin-right:10px; padding:5px 14px; border-radius:6px;" onclick="openStoreReg();"><?= lang('Admin.title_agency_reg') ?></button>
	</div>
    <div class="divList">
		<div class="divSearch" id="divSearchResult">Total 0</div>
		<table class="default_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_no') ?></th>
                    <th><?= lang('Admin.th_level') ?></th>
                    <th><?= lang('Admin.th_store_id') ?></th>
                    <th><?= lang('Admin.th_name') ?></th>
                    <th><?= lang('Admin.th_password') ?></th>
                    <th><?= lang('Admin.money_hold') ?></th>
                    <th><?= lang('Admin.point_hold') ?></th>
                    <th><?= lang('Admin.th_bet') ?></th>
                    <th><?= lang('Admin.th_bet_pl') ?></th>
                    <th><?= lang('Admin.th_round_limit') ?></th>
                    <th><?= lang('Admin.th_single_limit_short') ?></th>
                    <th><?= lang('Admin.th_mix_limit_short') ?></th>
                    <th><?= lang('Admin.th_egg_in') ?></th>
                    <th><?= lang('Admin.th_egg_out') ?></th>
                    <th><?= lang('Admin.th_charge_recall') ?></th>
                    <th><?= lang('Admin.th_edit') ?></th>
                    <th><?= lang('Admin.th_join_date') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList"></tbody>
        </table>
    </div>
</div>

<style>
.btn-store-action {
    display: inline-block; padding: 5px 12px; border-radius: 6px; border: none;
    color: #fff !important; font-size: 12px; font-weight: 600; cursor: pointer; margin: 1px;
}
.btn-store-blue { background: #1a73e8; }
.btn-store-red { background: #e53935; }
.btn-store-blue:hover { background: #1557b0; }
.btn-store-red:hover { background: #c62828; }
</style>

<script>
window.ADMIN_I18N = window.ADMIN_I18N || {};
window.ADMIN_I18N.btn_egg_charge = <?= json_encode(lang('Admin.btn_egg_charge'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_egg_recover = <?= json_encode(lang('Admin.btn_egg_recover'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_edit = <?= json_encode(lang('Admin.btn_edit'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_approve = <?= json_encode(lang('Admin.btn_approve'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.level_store = <?= json_encode(lang('Admin.menu_agency'), JSON_UNESCAPED_UNICODE) ?>;
</script>

<?php if($_ENV['CI_ENVIRONMENT'] == ENV_PRODUCTION) :?>
    <script src="/assets/js/agency.js?v=4"></script>
<?php else :?>
    <script src="/assets/js/agency.js?v=<?=time();?>"></script>
<?php endif ?>
