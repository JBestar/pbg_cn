
    <div style="position:absolute; left:0px; top:0px; width:100%; height:30px; line-height:30px; background-color:#283744; color:#ffffff; margin:0px; text-align:left; font-size:14px; font-weight:bold;">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<i class="far fa-circle"></i> <span id="spanUserName"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		
        <i class="far fa-circle"></i> <?= lang('Admin.money_hold') ?> : <span id="spanUserMoney" style="color:#ff0000;">0 원</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<i class="far fa-circle"></i> <?= lang('Admin.point_hold') ?> : <span id="spanUserPoint" style="color:#ff0000;">0 원</span>&nbsp;&nbsp;
		
    <?php if($mb_level == LEVEL_AGENCY) { ?>
		<span style="cursor: pointer;" onclick="pointToMoney();">[<?= lang('Admin.point_convert') ?>]</span>&nbsp;&nbsp;&nbsp;&nbsp;
        <i class="far fa-circle"></i> <?= lang('Admin.fee') ?> : <span id="spanGameRate" style="color:#ff0000;">0 %</span>&nbsp;&nbsp;
    <?php } ?>

		<i class="far fa-circle"></i> IP : <span id="spanUserIp"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<div style="position:absolute; left:calc(100% - 330px); top:5px; width:100px; height:20px; line-height:20px; text-align:center; color:#fefefe; background-color:#1b6b2c; cursor:pointer; display:none;" onclick="openPopup('http://bixolon.com/html/ko/download/download_product.xhtml?prod_id=122');">
			POS 설치파일
		</div>
		
    <?php if($mb_level == LEVEL_AGENCY) { ?>
		<div style="position:absolute; left:calc(100% - 440px); top:5px; width:100px; height:20px; line-height:20px; text-align:center; color:#fefefe; background-color:#1b6b2c; cursor:pointer;" onclick="clickMenu('sub_charge');">
			<?= lang('Admin.btn_charge') ?>
		</div>
		<div style="position:absolute; left:calc(100% - 330px); top:5px; width:100px; height:20px; line-height:20px; text-align:center; color:#fefefe; background-color:#1b6b2c; cursor:pointer;" onclick="clickMenu('sub_exchange');">
			<?= lang('Admin.btn_exchange') ?>
		</div>
    <?php } ?>

		<div style="position:absolute; left:calc(100% - 340px); top:5px; width:110px; height:24px; line-height:24px; text-align:center;">
			<select id="adminLangSelect" onchange="changeAdminLang(this.value);">
				<option value="ko" <?= (isset($admin_locale) && $admin_locale==='ko') ? 'selected' : '' ?>><?= lang('Admin.lang_ko') ?></option>
				<option value="zh" <?= (isset($admin_locale) && $admin_locale==='zh') ? 'selected' : '' ?>><?= lang('Admin.lang_zh') ?></option>
				<option value="en" <?= (isset($admin_locale) && $admin_locale==='en') ? 'selected' : '' ?>><?= lang('Admin.lang_en') ?></option>
			</select>
		</div>

		<div style="position:absolute; left:calc(100% - 220px); top:5px; width:100px; height:20px; line-height:20px; text-align:center; color:#fefefe; background-color:#0070bd; cursor:pointer;" onclick="clickMenu('setting');">
			<i class="fa fa-pencil-square-o"></i> <?= lang('Admin.setting') ?>
		</div>
        

		<div style="position:absolute; left:calc(100% - 110px); top:5px; width:100px; height:20px; line-height:20px; text-align:center; color:#fefefe; background-color:#cb251d; cursor:pointer;" onclick="logOut();">
			<i class="fas fa-sign-out-alt"></i> <?= lang('Admin.logout') ?>
		</div>
	</div>


    <div id="divLeftMenu" style="position:absolute; left:0px; top:30px; width:220px; height:calc(100% - 30px); background-color:#39435c; color:#ffffff; display:;">

        <div id="spanMainMenu0" class="spanLeftMenu <?=$menuitem_1?>" onclick="clickMenu('term_list');" ><i class="fas fa-calendar-alt"></i> <?= lang('Admin.menu_term') ?></div>

        <div class="spanLeftMenu"><i class="fas fa-gamepad"></i> <?= lang('Admin.menu_game') ?></div>
        <div id="spanMainMenu1" class="spanLeftSubMenu <?=$menuitem_2?>" onclick="clickMenu('game_result');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_game_result') ?></div>
        <div id="spanMainMenu2" class="spanLeftSubMenu <?=$menuitem_3?>" onclick="clickMenu('bet_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_bet_list') ?></div>

        <div class="spanLeftMenu"><i class="fas fa-sort-amount-down"></i> <?= lang('Admin.menu_sub') ?></div>
    <?php if($mb_level == LEVEL_AGENCY) { ?>
        <div id="spanMainMenu6" class="spanLeftSubMenu <?=$menuitem_5?>" onclick="clickMenu('member_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_agency') ?>&nbsp;&nbsp;</div>
    <?php } else if($mb_level > LEVEL_AGENCY) { ?>
        <div id="spanMainMenu6" class="spanLeftSubMenu <?=$menuitem_5?>" onclick="clickMenu('member_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_company') ?>&nbsp;&nbsp;</div>
    <?php }  ?>

        <div class="spanLeftMenu"><i class="fas fa-won-sign"></i> <?= lang('Admin.menu_money') ?></div>
        <div id="spanMainMenu8" class="spanLeftSubMenu <?=$menuitem_6?>" onclick="clickMenu('store_ce_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_store_ce') ?></div>
        <div id="spanMainMenu9" class="spanLeftSubMenu <?=$menuitem_7?>" onclick="clickMenu('agency_ce_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_agency_ce') ?></div>

        <?php if($mb_level == LEVEL_AGENCY) { ?>
        <div class="spanLeftMenu"><i class="fas fa-sticky-note"></i> <?= lang('Admin.menu_memo') ?></div>
        <div id="spanMainMenu11" class="spanLeftSubMenu <?=$menuitem_9?>" onclick="clickMenu('memo_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_memo_list') ?>&nbsp;&nbsp;</div>
        <div id="spanMainMenu12" class="spanLeftSubMenu <?=$menuitem_10?>" onclick="clickMenu('qna_list');"><i class="fa fa-arrow-right"></i> <?= lang('Admin.menu_qna') ?>&nbsp;&nbsp;
            <span id="spanQnaCnt" class="badge" style="background-color: rgb(153, 153, 153);">0</span>
        </div>
        <?php }  ?>

        <div class="spanLeftMenu" onclick="logOut();"><i class="fas fa-power-off"></i> <?= lang('Admin.logout') ?></div>
    </div>

<script>
function changeAdminLang(lang) {
    var jsonData = JSON.stringify({ lang: lang });
    $.ajax({
        url: '/api/set_lang',
        data: { json_: jsonData },
        type: 'post',
        dataType: 'json',
        success: function (jResult) {
            if (jResult.status === 'success') {
                location.reload();
            } else if (jResult.status === 'logout') {
                location.href = '/pages/login';
            }
        }
    });
}
</script>


    <div class="divContent">

        <table class="default_table">
            <tbody><tr>
                <th><?= lang('Admin.th_today_bet') ?></th>
                <th><?= lang('Admin.th_today_win') ?></th>
                <th><?= lang('Admin.th_today_point') ?></th>
                <th><?= lang('Admin.th_today_bet_pl') ?></th>
                <th><?= lang('Admin.th_today_charge') ?></th>
                <th><?= lang('Admin.th_today_exchange') ?></th>
                <th><?= lang('Admin.th_today_ce_pl') ?></th>
                <th><?= lang('Admin.th_month_egg_in') ?></th>
                <th><?= lang('Admin.th_month_egg_out') ?></th>
                <th><?= lang('Admin.th_month_charge') ?></th>
                <th><?= lang('Admin.th_month_exchange') ?></th>
                <th><?= lang('Admin.th_month_ce_pl') ?></th>
            </tr>
            <tbody id="tbAccount">
                <tr>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney"><font color="#0000fe">0</font></td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney">0</td>
                    <td class="tdMoney"><font color="#0000fe">0</font></td>
                </tr>
            </tbody>
        </table>

        <!--
        <script type="text/javascript">

            $(function(){
                getAccountInfo();
            });

            function getAccountInfo()
            {
                $.ajax({
                    url : "/Main/getAccountInfo",
                    type : "post",
                    cache : false,
                    async : true,
                    timeout : 10000,
                    scriptCharset : "utf-8",
                    dataType : "json",
                    success: function(res) {
                        
                        if( res.length > 0 )
                        {
                            addAccout(res[0]);
                        }
                        //console.log(res);
                    },
                    error: function(xhr,status,error) {
                        //alert("조회 실패 11111 => " + error);
                    }
                });

                setTimeout(function(){getAccountInfo();}, 12000);
            }

            function addAccout(data)
            {
                removeAllChild('tbAccount');
                
                var objTr = document.createElement('TR');
                objTr.appendChild(createTd('tdMoney', numberWithCommas(data.iTodayBet)));
                objTr.appendChild(createTd('tdMoney', numberWithCommas(data.iTodayWin)));
                objTr.appendChild(createTd('tdMoney', numberWithCommas(data.iTodayMyPoint)));
                var accTodayMoney = data.iTodayBet - data.iTodayWin - data.iTodayPoint;
                if( accTodayMoney < 0 )
                {
                    objTr.appendChild(createTd('tdMoney', "<font color='#fe0000'>" + numberWithCommas(accTodayMoney) + "</font>"));
                }
                else
                {
                    objTr.appendChild(createTd('tdMoney', "<font color='#0000fe'>" + numberWithCommas(accTodayMoney) + "</font>"));
                }
                objTr.appendChild(createTd('tdMoney', numberWithCommas(data.iTodayCharge)));
                objTr.appendChild(createTd('tdMoney', numberWithCommas(data.iTodayExchange)));
                var accChargeMoney = data.iTodayCharge - data.iTodayExchange;
                objTr.appendChild(createTd('tdMoney', numberWithCommas(accChargeMoney)));
                objTr.appendChild(createTd('tdMoney', numberWithCommas(data.iMonthServiceCharge)));
                objTr.appendChild(createTd('tdMoney', numberWithCommas(data.iMonthServiceExchange)));
                objTr.appendChild(createTd('tdMoney', numberWithCommas(data.iMonthCharge)));
                objTr.appendChild(createTd('tdMoney', numberWithCommas(data.iMonthExchange)));
                var accMonthMoney = data.iMonthCharge - data.iMonthExchange;
                if( accMonthMoney < 0 )
                {
                    objTr.appendChild(createTd('tdMoney', "<font color='#fe0000'>" + numberWithCommas(accMonthMoney) + "</font>"));
                }
                else
                {
                    objTr.appendChild(createTd('tdMoney', "<font color='#0000fe'>" + numberWithCommas(accMonthMoney) + "</font>"));
                }

                document.getElementById('tbAccount').appendChild(objTr);
            }

        </script>

        -->
