

    <div class="divTitle"><?= lang('Admin.menu_bet_sum') ?></div>
    <div class="divSearch">
    &nbsp;&nbsp;<?= lang('Admin.label_game_type') ?> : <select id="selectGameType" style="padding:2px 15px; min-height:24px;" name="selectGameType" onchange="changeGame();">
                    <option value="<?= GAME_POWER_BALL ?>" selected><?= lang('Admin.game_powerball') ?></option>
    </select>
    &nbsp;&nbsp;&nbsp;<?= lang('Admin.label_period') ?> : <input type="date" id="inputDateS" name="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
            <input type="date" id="inputDateE" name="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
			&nbsp;&nbsp;&nbsp;<span id="spanGameRound"><?= lang('Admin.label_game_round') ?></span> : <input type="text" id="inputGameNo" name="inputGameNo" class="inputDate">
			<button type="button" class="btn_search btn_icon" onclick="reqSearch();" title="<?= lang('Admin.btn_search') ?>" aria-label="<?= lang('Admin.btn_search') ?>"><i class="fas fa-search"></i></button>
		
	</div>
    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_game_date') ?></th>
                    <th><?= lang('Admin.th_game_round') ?></th>
                    <th><?= lang('Admin.th_day_round_full') ?></th>
                    <th><?= lang('Admin.th_bet_cnt') ?></th>
                    <th><?= lang('Admin.th_bet_sum') ?></th>
                    <th><?= lang('Admin.th_win_sum') ?></th>
                    <th><?= lang('Admin.th_point_sum') ?></th>
                    <th><?= lang('Admin.th_profit') ?></th>
                </tr>
            </thead>
            <tbody>
                
                <tr>
                    <th colspan="3"><?= lang('Admin.th_search_day_sum') ?></th>
                    <th id="thBetCount">0</th>
                    <th class="tdMoney" id="thBetSum">0</th>
                    <th class="tdMoney" id="thWinSum">0</th>
                    <th class="tdMoney" id="thPointSum">0</th>
                    <th class="tdMoney" id="thProfitSum" style="color:#0000fe;">0</th>
                </tr>
            </tbody>
            <tbody id="tbodyList">
                <!--
                <tr>
                    <th colspan="3"><?= lang('Admin.th_search_day_sum') ?></th>
                    <th id="thBetCount">0</th>
                    <th class="tdMoney" id="thBetSum">0</th>
                    <th class="tdMoney" id="thWinSum">0</th>
                    <th class="tdMoney" id="thPointSum">0</th>
                    <th class="tdMoney" id="thProfitSum" style="color:#0000fe;">1,768,200</th>
                </tr>
            </tbody>
            <tbody id="tbodyList">
                <tr>
                    <td class="tdDate">2021-10-17</td>
                    <td class="tdDate">1125861</td>
                    <td class="tdDate">285</td>
                    <td class="tdDate">4</td>
                    <td class="tdMoney">90,000</td>
                    <td class="tdMoney">57,900</td>
                    <td class="tdMoney">3,150</td>
                    <td class="tdMoney" style="color:#0000fe;">28,950</td>
                    
                </tr>
                
                <tr>
                    <td class="tdDate">2021-10-17</td>
                    <td class="tdDate">1125860</td>
                    <td class="tdDate">284</td>
                    <td class="tdDate">9</td>
                    <td class="tdMoney">1,160,000</td>
                    <td class="tdMoney">1,930,000</td>
                    <td class="tdMoney">40,600</td>
                    <td class="tdMoney" style="color:#fe0000;">-810,600</td>
                    
                </tr>
                -->
                
            </tbody>
        </table>
    
    </div>
    <div class="divPaging"></div>




<!-- <div class="divContent"> -->    
</div>

<script src="/assets/js/bet_sum.js"></script>