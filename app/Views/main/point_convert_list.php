
    <div class="divTitle"><?= lang('Admin.title_point_convert') ?></div>
    <div class="divSearch">
        &nbsp;&nbsp;<?= lang('Admin.label_period') ?> :
        <input type="date" id="inputDateS" value="<?php echo date('Y-m-d', strtotime('-1 month')); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
        <input type="date" id="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
        &nbsp;&nbsp;<?= lang('Admin.label_uid') ?> :
        <input type="text" id="inputUserID" class="inputDate" value="">
        &nbsp;&nbsp;<input type="submit" onclick="reqSearch();" class="btn_search" value="<?= lang('Admin.btn_search') ?>">
        <button class="btn_red" onclick="location.reload();"><?= lang('Admin.btn_refresh') ?></button>
	</div>

    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_no') ?>.</th>
                    <th><?= lang('Admin.th_uid') ?></th>
                    <th><?= lang('Admin.th_name') ?></th>
                    <th><?= lang('Admin.th_point_convert_amt') ?></th>
                    <th><?= lang('Admin.th_money_before') ?></th>
                    <th><?= lang('Admin.th_money_after') ?></th>
                    <th><?= lang('Admin.th_change_time') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList"></tbody>
        </table>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td id="tdPageNation" align="center" height="50">
                        <a class="light-theme" id="aPagePrev" href="javascript:prevPage();"><span class="light-theme">&lt;&lt;</span></a>
                        <span id="spanPaginationNum"></span>
                        <a class="light-theme" id="aPageNext" href="javascript:nextPage();"><span class="light-theme">&gt;&gt;</span></a>
                    </td>
                </tr>
            </tbody>
        </table>
	</div>
</div>

<?php if($_ENV['CI_ENVIRONMENT'] == ENV_PRODUCTION) :?>
    <script src="/assets/js/point_convert_list.js?v=1"></script>
<?php else :?>
    <script src="/assets/js/point_convert_list.js?v=<?=time();?>"></script>
<?php endif ?>
