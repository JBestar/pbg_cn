
<?php if($mb_level > LEVEL_AGENCY ) { ?>
    <div class="divTitle"><?= lang('Admin.title_setting') ?></div>

    <div class="divList" style="text-align:center;">
		<table class="default_table">
			
			<tbody>
                
                
                <tr>
                    <td style="width:120px; text-align:center;">싸이트 점검</td>
                    <td style="text-align:left;">
                        <select id="selectBetLock" style="width:82px;" >
                        <?php if(!$isMaintain) { ?>
                            <option value="0" selected>정상</option>
                            <option value="1" >점검</option>
                        <?php } else { ?>
                            <option value="0" >정상</option>
                            <option value="1" selected>점검</option>
                        <?php } ?>
                        </select>
                        &nbsp;&nbsp;<button class="btn_blue" onclick="changeBetLock();">변경</button>
                    </td>
                </tr>
                <tr>
                    
                    <td style="width:120px; text-align:center;">배팅 마감시간</td>
                    <td style="text-align:left;">
                        <div>보글볼 : 
                            <input id="inputBetTime" type="number" style="width:60px; text-align:left;" value="<?=$bet_time3?>">&nbsp;초
                            &nbsp;&nbsp;<button class="btn_blue" onclick="changeBetTime(<?=GAME_BOGLE_BALL?>);">변경</button>
                        </div>
                        <div style="margin-top:5px;">코인볼 : 
                            <input id="inputBetTime1" type="number" style="width:60px; text-align:left;" value="<?=$bet_time2?>">&nbsp;초
                            &nbsp;&nbsp;<button class="btn_blue" onclick="changeBetTime(<?=GAME_COIN5_BALL?>);">변경</button>
                        </div>
                    </td>
                </tr>

                <tr>                    
                    <td style="width:120px; text-align:center;">디비정리</td>
                    <td style="text-align:left;">
                        <input id="inputHistoryTime" type="date" style="width:120px; text-align:left;" value="<?php echo date('Y-m').'-01'; ?>">&nbsp;이전내역
                        &nbsp;&nbsp;<button class="btn_red" id="btnClean" onclick="cleanDb();">정리</button>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
    <?php } ?>

    <?php if($mb_level == LEVEL_AGENCY ) { ?>
    <div class="divTitle">입금계좌 설정</div>

    <div class="divList" style="text-align:center;">
			
		<table class="default_table">
			<tbody>
                <tr>
                   
                    <td style="width:80px; text-align:center;">은행명</td>
                    <td style="text-align:left;">
                        <input id="inputBankName" type="text" style="width:80px; text-align:left;" value="<?=$mb_bank_name?>">&nbsp;
                    </td>
                    <td style="width:80px; text-align:center;">예금주</td>
                    <td style="text-align:left;">
                        <input id="inputBankOwner" type="text" style="width:120px; text-align:left;" value="<?=$mb_bank_owner?>">&nbsp;
                    </td>
                    <td style="width:80px; text-align:center;">계좌번호</td>
                    <td style="text-align:left;">
                        <input id="inputBankNum" type="text" style="width:160px; text-align:left;" value="<?=$mb_bank_num?>">&nbsp;
                        &nbsp;&nbsp;<button class="btn_blue" onclick="setBankInfo();">변경</button>
                    </td>
                </tr>
            </tbody>
        </table>
			
	</div>
    <?php } ?>

    <div class="divTitle" style="margin-top:20px;">정보수정</div>

    <div class="divList" style="text-align:center;">
		<table class="default_table" style="text-align:left;">
			<tbody><tr>
				<td style="width:120px; text-align:center;">현재비밀번호</td>
				<td>
					<input id="inputCurrentPwd" type="password" style="width:120px;">
				</td>
			</tr>
			<tr>
				<td style="text-align:center;">새 비밀번호</td>
				<td>
					<input id="inputNewPwd" type="password" style="width:120px;">
				</td>
			</tr>
			<tr>
				<td style="text-align:center;">새 비밀번호 확인</td>
				<td>
					<input id="inputNewPwd2" type="password" style="width:120px;">
				</td>
			</tr>
		</tbody></table>
		<button class="btn_blue" style="margin: 10px 0px 10px 0px;" onclick="changeInfo();">정보변경</button>
	</div>




<!-- <div class="divContent"> -->    
</div>


<script src="/assets/js/setting.js"></script>