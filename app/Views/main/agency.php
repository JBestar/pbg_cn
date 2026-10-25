
    <div class="divTitle"><?= lang('Admin.title_agency_mgmt') ?></div>
    <div class="divSearch">
			<input type="date" id="inputDateS" name="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
            <input type="date" id="inputDateE" name="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
			&nbsp;&nbsp;&nbsp;<?= lang('Admin.label_uid') ?> : <input type="text" id="inputSubId" name="inputSubId" class="inputDate" value="" autocomplete="off">
			<button type="button" class="btn_search btn_icon" onclick="reqSearch();" title="<?= lang('Admin.btn_search') ?>" aria-label="<?= lang('Admin.btn_search') ?>"><i class="fas fa-search"></i></button>&nbsp;
            <button type="button" class="btn_reg" style="float:right; margin-right:10px;" onclick="openStoreReg();"><?= lang('Admin.title_agency_reg') ?></button>
	</div>
    <div class="divList">
		<table class="default_table nowrap_table">
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
                    <th><?= lang('Admin.th_edit_delete') ?></th>
                    <th><?= lang('Admin.th_join_date') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList"></tbody>
        </table>
    </div>
</div>

<style>
.btn-store-action {
    margin: 1px;
}
#tbodyList td.store-uid-cell {
    position: relative;
    text-align: left;
    padding-left: 8px;
}
.store-tree-uid {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}
.store-tree-vline,
.store-tree-hline {
    position: absolute;
    pointer-events: none;
}
.store-tree-vline {
    top: -1px;
    bottom: -1px;
    width: 0;
    border-left: 1px solid #999;
}
.store-tree-vline-top {
    bottom: 50%;
}
.store-tree-vline-bottom {
    top: calc(50% + 8px);
}
.store-tree-hline {
    top: 50%;
    height: 0;
    border-top: 1px solid #999;
}
#tbodyList td button.store-tree-toggle {
    width: 16px;
    min-width: 16px;
    max-width: 16px;
    height: 16px;
    min-height: 16px;
    max-height: 16px;
    margin-right: 4px;
    padding: 0;
    box-sizing: border-box;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 16px;
    border: 1px solid #999;
    border-radius: 2px;
    background: #fff;
    color: #333;
    font: 700 12px/1 monospace;
    cursor: pointer;
}
.store-tree-spacer {
    display: inline-block;
    width: 20px;
}
</style>

<script>
window.ADMIN_I18N = window.ADMIN_I18N || {};
window.ADMIN_I18N.btn_egg_charge = <?= json_encode(lang('Admin.btn_egg_charge'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_egg_recover = <?= json_encode(lang('Admin.btn_egg_recover'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_edit = <?= json_encode(lang('Admin.btn_edit'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_delete = <?= json_encode(lang('Admin.btn_delete'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.msg_delete_store = <?= json_encode(lang('Admin.msg_delete_store'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.level_store = <?= json_encode(lang('Admin.menu_agency'), JSON_UNESCAPED_UNICODE) ?>;
</script>

<?php if($_ENV['CI_ENVIRONMENT'] == ENV_PRODUCTION) :?>
    <script src="/assets/js/agency.js?v=12"></script>
<?php else :?>
    <script src="/assets/js/agency.js?v=<?=time();?>"></script>
<?php endif ?>
