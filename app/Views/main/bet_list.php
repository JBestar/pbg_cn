

    <div class="divTitle"><?= lang('Admin.menu_bet_list') ?></div>
    <div class="divSearch">
    &nbsp;&nbsp;<?= lang('Admin.label_game_type') ?> : <select id="selectGameType" style="padding:2px 15px; min-height:24px;" name="selectGameType" onchange="changeGame();">
                    <option value="<?= GAME_POWER_BALL ?>" selected><?= lang('Admin.game_powerball') ?></option>
    </select>
	&nbsp;&nbsp;&nbsp;<?= lang('Admin.label_period') ?> : <input type="date" id="inputDateS" name="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
            <input type="date" id="inputDateE" name="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
			&nbsp;&nbsp;&nbsp;<span id="spanGameRound"><?= lang('Admin.label_game_round') ?></span> : <input type="text" id="inputGameNo" name="inputGameNo" class="inputDate" value="">
			&nbsp;&nbsp;&nbsp;<?= lang('Admin.label_uid') ?> : <input type="text" id="inputUserID" name="inputUserID" class="inputDate" value="">
			&nbsp;&nbsp;<input type="submit" onclick="reqSearch();" class="btn_search" value="<?= lang('Admin.btn_search') ?>">
		
	</div>
    <link rel="stylesheet" href="/assets/css/button.css">
    <link rel="stylesheet" href="/assets/css/button.new.css">

    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_game') ?></th>
                    <th><?= lang('Admin.th_game_round') ?></th>
                    <th><?= lang('Admin.th_draw_nums') ?></th>
                    <th><?= lang('Admin.th_num_sum') ?></th>
                    <th><?= lang('Admin.th_draw_result') ?></th>
                    <th><?= lang('Admin.th_belong') ?></th>
                    <th><?= lang('Admin.th_uid') ?></th>
                    <th><?= lang('Admin.th_name') ?></th>
                    <th><?= lang('Admin.th_ip') ?></th>
                    <th><?= lang('Admin.th_user_bet') ?></th>
                    <th><?= lang('Admin.th_money_before') ?></th>
                    <th><?= lang('Admin.th_bet_amount') ?></th>
                    <th><?= lang('Admin.th_money_after') ?></th>
                    <th><?= lang('Admin.th_win_money') ?></th>
                    <th><?= lang('Admin.th_win_result') ?></th>
                    <th><?= lang('Admin.th_bet_time') ?></th>
                    
                </tr>
            </thead>
            <tbody id="tbodyList">
                
            </tbody>
        </table>

		<table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td id="tdPageNation" align="center" height="50">
                        <a class="light-theme" id="aPagePrev" href="javascript:prevPage();"><span class="light-theme"><<</span></a>
                        <span id="spanPaginationNum">
                            <!--
                            <a class="light-theme" ><span class="current">1</span></a>
                            <a class="light-theme" ><span class="light-theme">2</span></a>
                            -->
                        </span>
                        <a class="light-theme" id="aPageNext" href="javascript:nextPage();"><span class="light-theme">>></span></a>
                    </td>
                </tr>
            </tbody>
        </table>

	</div>

    <div id="divEditBet" style="position:absolute; left:calc(50% - 340px); top:30px; width:680px; border:1px solid #010101; background-color:#fefefe; display:none;">
        <div class="divTitle" id="divEditBetTitle">배팅수정
            <button class="btn_red" style="position:absolute; right:5px; top:5px; font-size:18px;" onclick="closeEditBet();">&times;</button>
        </div>
        <div class="divInfoBox" style="line-height:34px; font-size:14px;">
            <table style="width:100%; border:0px;">
                <tbody>
                    <tr>
                        <td id="tdUserInfo" style="width:200px"></td>
                        <td id="tdUserBet" style="width:250px"></td>
                        <td id="tdEditBet" style="width:150px"></td>
                    </tr>
                    <tr>
                        
                        <td style="border:0px; width:600px; text-align:center;"  colspan="3">
                            <div class="divBetTitle" style="">일반볼 게임</div>

                            <div id="divBetTypeNew_0" class="btnGameSelect" style="width:150px;" onclick="setBetType(0);"><div class="divMarkOdd">홀</div></div>
                            <div id="divBetTypeNew_1" class="btnGameSelect" style="width:150px;" onclick="setBetType(1);"><div class="divMarkEven">짝</div></div>
                            <div id="divBetTypeNew_2" class="btnGameSelect" style="width:150px;" onclick="setBetType(2);"><div class="divMarkUnder">언더</div></div>
                            <div id="divBetTypeNew_3" class="btnGameSelect" style="width:150px;" onclick="setBetType(3);"><div class="divMarkOver">오버</div></div>

                            <div id="divBetTypeNew_8" class="btnGameSelect" style="width:200px;" onclick="setBetType(8);"><div class="divMarkBig">대</div></div>
                            <div id="divBetTypeNew_9" class="btnGameSelect" style="width:205px;" onclick="setBetType(9);"><div class="divMarkBig">중</div></div>
                            <div id="divBetTypeNew_10" class="btnGameSelect" style="width:200px;" onclick="setBetType(10);"><div class="divMarkBig">소</div></div>

                            <div id="divBetTypeNew_4" class="btnGameSelect" style="width:120px;" onclick="setBetType(4);"><div class="divMarkOdd">홀</div><div class="divMarkUnder">언더</div></div>
                            <div id="divBetTypeNew_6" class="btnGameSelect" style="width:120px;" onclick="setBetType(6);"><div class="divMarkOdd">홀</div><div class="divMarkOver">오버</div></div>
                            <div id="divBetTypeNew_11" class="btnGameSelect" style="width:120px;" onclick="setBetType(11);"><div class="divMarkOdd">홀</div><div class="divMarkBig">대</div></div>
                            <div id="divBetTypeNew_12" class="btnGameSelect" style="width:120px;" onclick="setBetType(12);"><div class="divMarkOdd">홀</div><div class="divMarkBig">중</div></div>
                            <div id="divBetTypeNew_13" class="btnGameSelect" style="width:120px;" onclick="setBetType(13);"><div class="divMarkOdd">홀</div><div class="divMarkBig">소</div></div>

                            <div id="divBetTypeNew_5" class="btnGameSelect" style="width:120px;" onclick="setBetType(5);"><div class="divMarkEven">짝</div><div class="divMarkUnder">언더</div></div>
                            <div id="divBetTypeNew_7" class="btnGameSelect" style="width:120px;" onclick="setBetType(7);"><div class="divMarkEven">짝</div><div class="divMarkOver">오버</div></div>
                            <div id="divBetTypeNew_14" class="btnGameSelect" style="width:120px;" onclick="setBetType(14);"><div class="divMarkEven">짝</div><div class="divMarkBig">대</div></div>
                            <div id="divBetTypeNew_15" class="btnGameSelect" style="width:120px;" onclick="setBetType(15);"><div class="divMarkEven">짝</div><div class="divMarkBig">중</div></div>
                            <div id="divBetTypeNew_16" class="btnGameSelect" style="width:120px;" onclick="setBetType(16);"><div class="divMarkEven">짝</div><div class="divMarkBig">소</div></div>

                            <div class="divBetTitle" style="">파워볼 게임</div>
                    
                            <div id="divBetTypeNew_17" class="btnGameSelect" style="width:150px;" onclick="setBetType(17);"><div class="divMarkPower">파워볼</div><div class="divMarkOdd">홀</div></div>
                            <div id="divBetTypeNew_18" class="btnGameSelect" style="width:150px;" onclick="setBetType(18);"><div class="divMarkPower">파워볼</div><div class="divMarkEven">짝</div></div>
                            <div id="divBetTypeNew_19" class="btnGameSelect" style="width:150px;" onclick="setBetType(19);"><div class="divMarkPower">파워볼</div><div class="divMarkUnder">언더</div></div>
                            <div id="divBetTypeNew_20" class="btnGameSelect" style="width:150px;" onclick="setBetType(20);"><div class="divMarkPower">파워볼</div><div class="divMarkOver">오버</div></div>

                            <div id="divBetTypeNew_21" class="btnGameSelect" style="width:150px;" onclick="setBetType(21);"><div class="divMarkPower">파워볼</div><div class="divMarkOdd">홀</div><div class="divMarkUnder">언더</div></div>
                            <div id="divBetTypeNew_22" class="btnGameSelect" style="width:150px;" onclick="setBetType(22);"><div class="divMarkPower">파워볼</div><div class="divMarkEven">짝</div><div class="divMarkUnder">언더</div></div>
                            <div id="divBetTypeNew_23" class="btnGameSelect" style="width:150px;" onclick="setBetType(23);"><div class="divMarkPower">파워볼</div><div class="divMarkOdd">홀</div><div class="divMarkOver">오버</div></div>
                            <div id="divBetTypeNew_24" class="btnGameSelect" style="width:150px;" onclick="setBetType(24);"><div class="divMarkPower">파워볼</div><div class="divMarkEven">짝</div><div class="divMarkOver">오버</div></div>

                            <div class="divBetTitle" style="">일반볼 + 파워볼 조합</div>
                
                            <div id="divBetTypeNew_33" class="btnGameSelect" style="width:150px;" onclick="setBetType(33);">
                                <div class="divMarkOdd">홀</div>&nbsp;&nbsp;
                                <div class="divMarkUnder">언더</div>&nbsp;&nbsp;
                                <div class="divMarkOdd">파홀</div>
                            </div>
                            <div id="divBetTypeNew_34" class="btnGameSelect" style="width:150px;" onclick="setBetType(34);">
                                <div class="divMarkOdd">홀</div>&nbsp;&nbsp;
                                <div class="divMarkUnder">언더</div>&nbsp;&nbsp;
                                <div class="divMarkEven">파짝</div>
                            </div>
                            <div id="divBetTypeNew_35" class="btnGameSelect" style="width:150px;" onclick="setBetType(35);">
                                <div class="divMarkOdd">홀</div>&nbsp;&nbsp;
                                <div class="divMarkOver">오버</div>&nbsp;&nbsp;
                                <div class="divMarkOdd">파홀</div>
                            </div>
                            <div id="divBetTypeNew_36" class="btnGameSelect" style="width:150px;" onclick="setBetType(36);">
                                <div class="divMarkOdd">홀</div>&nbsp;&nbsp;
                                <div class="divMarkOver">오버</div>&nbsp;&nbsp;
                                <div class="divMarkEven">파짝</div>
                            </div>
                            <div id="divBetTypeNew_37" class="btnGameSelect" style="width:150px;" onclick="setBetType(37);">
                                <div class="divMarkEven">짝</div>&nbsp;&nbsp;
                                <div class="divMarkUnder">언더</div>&nbsp;&nbsp;
                                <div class="divMarkOdd">파홀</div>
                            </div>
                            <div id="divBetTypeNew_38" class="btnGameSelect" style="width:150px;" onclick="setBetType(38);">
                                <div class="divMarkEven">짝</div>&nbsp;&nbsp;
                                <div class="divMarkUnder">언더</div>&nbsp;&nbsp;
                                <div class="divMarkEven">파짝</div>
                            </div>
                            <div id="divBetTypeNew_39" class="btnGameSelect" style="width:150px;" onclick="setBetType(39);">
                                <div class="divMarkEven">짝</div>&nbsp;&nbsp;
                                <div class="divMarkOver">오버</div>&nbsp;&nbsp;
                                <div class="divMarkOdd">파홀</div>
                            </div>
                            <div id="divBetTypeNew_40" class="btnGameSelect" style="width:150px;" onclick="setBetType(40);">
                                <div class="divMarkEven">짝</div>&nbsp;&nbsp;
                                <div class="divMarkOver">오버</div>&nbsp;&nbsp;
                                <div class="divMarkEven">파짝</div>
                            </div>
                            <div class="divBetTitle" >파워볼 번호</div>
                            <div id="divBetTypeNew_41" class="btnGameSelect" style="width:55px;" onclick="setBetType(41);">
                                <div class="divMarkBig" style="font-size:24px;">0</div>
                            </div>
                            <div id="divBetTypeNew_42" class="btnGameSelect" style="width:55px;" onclick="setBetType(42);">
                                <div class="divMarkBig" style="font-size:24px;">1</div>                                
                            </div>
                            <div id="divBetTypeNew_43" class="btnGameSelect" style="width:55px;" onclick="setBetType(43);">
                                <div class="divMarkBig" style="font-size:24px;">2</div>                                
                            </div>
                            <div id="divBetTypeNew_44" class="btnGameSelect" style="width:55px;" onclick="setBetType(44);">
                                <div class="divMarkBig" style="font-size:24px;">3</div>                                
                            </div>
                            <div id="divBetTypeNew_45" class="btnGameSelect" style="width:55px;" onclick="setBetType(45);">
                                <div class="divMarkBig" style="font-size:24px;">4</div>                                
                            </div>

                            <div id="divBetTypeNew_46" class="btnGameSelect" style="width:55px;" onclick="setBetType(46);">
                                <div class="divMarkBig" style="font-size:24px;">5</div>                                
                            </div>
                            <div id="divBetTypeNew_47" class="btnGameSelect" style="width:55px;" onclick="setBetType(47);">
                                <div class="divMarkBig" style="font-size:24px;">6</div>                                
                            </div>
                            <div id="divBetTypeNew_48" class="btnGameSelect" style="width:55px;" onclick="setBetType(48);">
                                <div class="divMarkBig" style="font-size:24px;">7</div>                                
                            </div>
                            <div id="divBetTypeNew_49" class="btnGameSelect" style="width:55px;" onclick="setBetType(49);">
                                <div class="divMarkBig" style="font-size:24px;">8</div>                                
                            </div>
                            <div id="divBetTypeNew_50" class="btnGameSelect" style="width:55px;" onclick="setBetType(50);">
                                <div class="divMarkBig" style="font-size:24px;">9</div>                                
                            </div>


                        </td>
                    </tr>
                    <tr>
                        <td style="border:0px; text-align:center;" colspan="3">
                            <button class="btn_search" onclick="saveEditBet();">저장</button>&nbsp;
                            <button class="btn_red" onclick="closeEditBet();">닫기</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<!-- <div class="divContent"> -->    
</div>


<?php if($_ENV['CI_ENVIRONMENT'] == ENV_PRODUCTION) :?>
    <script src="/assets/js/bet_list.js?v=1"></script>
<?php else :?>
    <script src="/assets/js/bet_list.js?v=<?=time();?>"></script>
<?php endif ?>

