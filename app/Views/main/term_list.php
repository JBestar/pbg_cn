
    <div class="divTitle"><?= lang('Admin.menu_term') ?></div>
        
    <div class="divSearch">
            <select id="selectLevel" style="padding:2px 10px; min-width:150px; min-height:24px;" name="selectLevel">
            <?php if($adminLevel == LEVEL_AGENCY ) { ?>
                <option value=""><?= lang('Admin.opt_stats_store') ?></option>    
            <?php } else {?>
                <option value=""><?= lang('Admin.opt_stats_agency') ?></option>    
            <?php } ?>
            <?php foreach ($arrMember as $objMember):?>
                <option value="<?=$objMember->mb_uid?>"><?=$objMember->mb_uid?> (<?=$objMember->mb_nickname?>)</option>    
            <?php endforeach;?>            
            </select>
            
            <?php
            $dateS = ($adminLevel == LEVEL_AGENCY) ? date('Y-m-d') : date('Y-m-d', strtotime('-1 month'));
            $dateE = date('Y-m-d');
            ?>
            <input type="date" id="inputDateS" value="<?= $dateS ?>" name="inputDateS" class="inputDate hasDatepicker">&nbsp;~&nbsp;
            <input type="date" id="inputDateE" value="<?= $dateE ?>" name="inputDateE" class="inputDate hasDatepicker">
            <input type="submit" class="btn_search" onclick="reqSearch();" value="<?= lang('Admin.btn_search') ?>">
    </div>


    <div class="divList">
        <table class="default_table">
            <thead>
                <tr>
                    <th><?= lang('Admin.th_date') ?></th>
                    <th><?= lang('Admin.th_bet_amount') ?></th>
                    <th><?= lang('Admin.th_win_amount') ?></th>
                    <th><?= lang('Admin.th_point') ?></th>
                    <th><?= lang('Admin.th_bet_pl') ?></th>
                    <th><?= lang('Admin.th_egg_in') ?></th>
                    <th><?= lang('Admin.th_egg_out') ?></th>
                    <?php if ($adminLevel > LEVEL_AGENCY) { ?>
                    <th><?= lang('Admin.th_charge') ?></th>
                    <th><?= lang('Admin.th_exchange') ?></th>
                    <?php } ?>
                    <th><?= lang('Admin.th_ce_pl') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList"></tbody>
        </table>
    </div>

<script>
window.TERM_ADMIN_LEVEL = <?= (int)$adminLevel ?>;
window.ADMIN_I18N = window.ADMIN_I18N || {};
window.ADMIN_I18N.th_total = <?= json_encode(lang('Admin.th_total'), JSON_UNESCAPED_UNICODE) ?>;
</script>
<?php if($_ENV['CI_ENVIRONMENT'] == ENV_PRODUCTION) :?>
    <script src="/assets/js/term_list.js?v=3"></script>
<?php else :?>
    <script src="/assets/js/term_list.js?v=<?=time();?>"></script>
<?php endif ?>
