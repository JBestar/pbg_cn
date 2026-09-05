
    <div class="divTitle"><?= lang('Admin.menu_game_result') ?></div>
    <div class="divSearch">
        <select id="selectLevel" style="padding:2px 10px; min-width:150px; min-height:24px;" name="selectLevel">
        <?php if ($adminLevel == LEVEL_AGENCY) { ?>
            <option value=""><?= lang('Admin.opt_stats_store') ?></option>
        <?php } else { ?>
            <option value=""><?= lang('Admin.opt_stats_agency') ?></option>
        <?php } ?>
        <?php foreach ($arrMember as $objSub): ?>
            <option value="<?= esc($objSub->mb_uid) ?>"><?= esc($objSub->mb_uid) ?> (<?= esc($objSub->mb_nickname) ?>)</option>
        <?php endforeach; ?>
        </select>
        &nbsp;&nbsp;&nbsp;<?= lang('Admin.label_period') ?> :
        <input type="date" id="inputDateS" name="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
        <input type="date" id="inputDateE" name="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
        &nbsp;&nbsp;&nbsp;<span id="spanGameRound"><?= lang('Admin.label_game_round') ?></span> :
        <input type="text" id="inputGameNo" name="inputGameNo" class="inputDate" value="">
        <input type="submit" class="btn_search" onclick="reqSearch();" value="<?= lang('Admin.btn_search') ?>">
	</div>
    <div class="divList">
		<table class="default_table">
            <tbody>
                <tr id="theadRow">
                    <th><?= lang('Admin.th_game_round') ?></th>
                    <th><?= lang('Admin.th_day_round') ?></th>
                    <th><?= lang('Admin.th_draw_nums') ?></th>
                    <th><?= lang('Admin.th_num_sum') ?></th>
                    <th><?= lang('Admin.th_time') ?></th>
                    <th><?= lang('Admin.th_bet_count') ?></th>
                    <th><?= lang('Admin.th_bet_amount') ?></th>
                    <th><?= lang('Admin.th_win_amount') ?></th>
                    <th><?= lang('Admin.th_point') ?></th>
                    <th><?= lang('Admin.th_settle') ?></th>
                </tr>
            </tbody>
            <tbody id="tbodyList"></tbody>
        </table>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td id="tdPageNation" align="center" height="50">
                        <a class="light-theme" id="aPagePrev" href="javascript:prevPage();"><span class="light-theme"><<</span></a>
                        <span id="spanPaginationNum"></span>
                        <a class="light-theme" id="aPageNext" href="javascript:nextPage();"><span class="light-theme">>></span></a>
                    </td>
                </tr>
            </tbody>
        </table>
	</div>
</div>

<?php if($_ENV['CI_ENVIRONMENT'] == ENV_PRODUCTION) :?>
    <script src="/assets/js/game_result.js?v=3"></script>
<?php else :?>
    <script src="/assets/js/game_result.js?v=<?=time();?>"></script>
<?php endif ?>
