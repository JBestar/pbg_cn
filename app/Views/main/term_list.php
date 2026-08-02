

        <div class="divTitle"><?= lang('Admin.menu_term') ?></div>
        
        <div class="divSearch">
            <!--
            <form id="formSearch" method="get" action="/Main/term_list">
            -->
                <select id="selectLevel" style="padding:2px 10px; min-width 150px; min-height:24px;" name="selectLevel">
                <?php if($adminLevel == LEVEL_AGENCY ) { ?>
                    <option value=""><?= lang('Admin.opt_stats_store') ?></option>    
                <?php } else {?>
                    <option value=""><?= lang('Admin.opt_stats_agency') ?></option>    
                <?php } ?>
                <?php foreach ($arrMember as $objMember):?>
                    <option value="<?=$objMember->mb_uid?>"><?=$objMember->mb_uid?> (<?=$objMember->mb_nickname?>)</option>    
                <?php endforeach;?>            
                </select>
                
                <input type="date" id="inputDateS" value="<?php echo date('Y-m-d'); ?>" name="inputDateS" class="inputDate hasDatepicker">&nbsp;~&nbsp;
                <input type="date" id="inputDateE" value="<?php echo date('Y-m-d'); ?>" name="inputDateE" class="inputDate hasDatepicker">
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
                        <th><?= lang('Admin.th_charge') ?></th>
                        <th><?= lang('Admin.th_exchange') ?></th>
                        <th><?= lang('Admin.th_ce_pl') ?></th>
                    </tr>
                </thead>
                <tbody id="tbodyList">
                    <!--
                    <tr>
                        <td class="tdDate">2021-10-17</td>
                        <td class="tdMoney">19,170,000</td>
                        <td class="tdMoney">19,019,700</td>
                        <td class="tdMoney">670,950</td>
                        <td class="tdMoney" style="color:#fe0000;">-520,650</td>
                        <td class="tdMoney">4,200,000</td>
                        <td class="tdMoney">0</td>
                        <td class="tdMoney">1,000,000</td>
                        <td class="tdMoney">2,100,000</td>
                        <td class="tdMoney" style="color:#fe0000;">-1,100,000</td>
                    </tr>

                    
                    <tr>
                        <td class="tdSum tdDate">합계</td>
                        <td class="tdSum tdMoney">19,170,000</td>
                        <td class="tdSum tdMoney">19,019,700</td>
                        <td class="tdSum tdMoney">670,950</td>
                        <td class="tdSum tdMoney" style="color:#fe0000;">-520,650</td>
                        
                        <td class="tdSum tdMoney">4,200,000</td>
                        <td class="tdSum tdMoney">0</td>
                        <td class="tdSum tdMoney">1,000,000</td>
                        <td class="tdSum tdMoney">2,100,000</td>
                        <td class="tdSum tdMoney" style="color:#fe0000;">-1,100,000</td>
                        
                    </tr>
                -->
                </tbody>
            </table>
        </div>



    <!-- <div class="divContent"> -->    
    </div>


    <script src="/assets/js/term_list.js"></script>
