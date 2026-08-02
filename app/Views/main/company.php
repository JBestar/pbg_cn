

    <div class="divTitle"><?= lang('Admin.title_company_mgmt') ?></div>
    <div class="divSearch">
        <!--
		<form name="formSearch" method="get" action="/Main/sub_list">
        <input type="hidden" name="sub_level" value="1">
		-->	
			<input type="date" id="inputDateS" name="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
            <input type="date" id="inputDateE" name="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
			&nbsp;&nbsp;&nbsp;<?= lang('Admin.label_uid') ?> : <input type="text" id="inputSubId" name="inputSubId" class="inputDate" value="" autocomplete="off">
			<input type="submit" class="btn_search" onclick="reqSearch();" value="<?= lang('Admin.btn_search') ?>">&nbsp;
            <button class="submit" style="float:right; margin-right:10px; padding:5px 10px;" onclick="showRegMember();"><?= lang('Admin.title_company_reg') ?></button>
		
	</div>
    <div class="divList">
		<div class="divSearch" id="divSearchResult">Total 0</div>
		<table class="default_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_no') ?></th>
                    <th><?= lang('Admin.th_level') ?></th>
                    <th><?= lang('Admin.th_uid') ?></th>
                    <th><?= lang('Admin.th_name') ?></th>
                    <th><?= lang('Admin.money_hold') ?></th>
                    <th><?= lang('Admin.point_hold') ?></th>                    
                    <th><?= lang('Admin.th_sub_money_sum') ?></th>

                    <th><?= lang('Admin.th_bet') ?></th>
                    <th><?= lang('Admin.th_win') ?></th>
                    <th><?= lang('Admin.th_point') ?></th>
                    <th><?= lang('Admin.th_bet_pl') ?></th>
                    <th><?= lang('Admin.th_charge') ?></th>
                    <th><?= lang('Admin.th_exchange') ?></th>
                    <th><?= lang('Admin.th_ce_pl') ?></th>
                    <th><?= lang('Admin.th_egg_in') ?></th>
                    <th><?= lang('Admin.th_egg_out') ?></th>

                    <th><?= lang('Admin.th_fee_pct') ?></th>
                    <th><?= lang('Admin.th_store_cnt') ?></th>
                    <th><?= lang('Admin.th_charge_recall') ?></th>
                    <th><?= lang('Admin.th_edit') ?></th>
                    <th><?= lang('Admin.th_delete') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList">

            </tbody>
        </table>
	</div>

    <div id="divRegSub" style="position:absolute; left:calc(50% - 300px); top:100px; width:600px; border:1px solid #010101; background-color:#fefefe; display:none;">
		<div class="divTitle">총판등록</div>
        <div class="divInfoBox" style="line-height:34px; font-size:14px;">
            <table style="width:100%; border:0px;">
                
                <tbody>
                    <tr>
                        <td style="width:120px; border:0px;">아이디</td>
                        <td style="width:150px; border:0px;"><input type="text" id="regSubId" style="width:120px;"></td>
                        <td style="border:0px;">* 중복 아이디 불가</td>
                    </tr>
                    <tr>
                        <td style="width:120px; border:0px;">이름</td>
                        <td style="width:150px; border:0px;"><input type="text" id="regSubName" style="width:120px;"></td>
                        <td style="border:0px;">* 중복 이름 불가</td>
                    </tr>
                    <tr>
                        <td style="width:120px; border:0px;">비밀번호</td>
                        <td style="width:150px; border:0px;"><input type="password" id="regSubPwd" style="width:120px;"></td>
                        <td style="border:0px;"></td>
                    </tr>
                    <tr>
                        <td style="width:120px; border:0px;">출금 비밀번호</td>
                        <td style="width:150px; border:0px;"><input type="password" id="regSubExcPwd" style="width:120px;"></td>
                        <td style="border:0px;"></td>
                    </tr>
                    <tr>
                        <td style="width:120px; border:0px;">휴대전화</td>
                        <td style="width:150px; border:0px;"><input type="text" id="regSubPhone" style="width:120px;"></td>
                        <td style="border:0px;"></td>
                    </tr>
                    <tr>
                        <td style="width:120px; border:0px;">계좌정보</td>
                        <td style="border:0px;" colspan="2">
                            <input type="text" id="regSubBank" style="width:120px;" placeholder="은행명">
                            <input type="text" id="regSubBankNum" style="width:160px;" placeholder="계좌번호">
                            <input type="text" id="regSubBankOwner" style="width:120px;" placeholder="예금주">
                        </td>
                    </tr>

                    <tr>
                        <td style="width:120px; border:0px;">수수료</td>
                        <td style="width:150px; border:0px;"><input type="text" id="regSubSingleDealRate" style="width:120px;"> %</td>
                        <td style="border:0px;"></td>
                    </tr>
                    <!--
                    <tr>
                        <td style="width:120px; border:0px;">조합 수수료</td>
                        <td style="width:150px; border:0px;"><input type="text" id="regSubMultiDealRate" style="width:120px;"> %</td>
                        <td style="border:0px;"></td>
                    </tr>
                    <input type="hidden" id="regSubDomain" value="">
                    -->
                    <tr>
                        <td style="border:0px; text-align:center;" colspan="3">
                            <button class="btn_search" onclick="reqRegMember();">등록</button>&nbsp;
                            <button class="btn_red" onclick="closeRegMember();">닫기</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<!-- <div class="divContent"> -->    
</div>


<div id="divSubMember" style="position:absolute; left:calc(50% - 350px); top:150px; width:700px; border:1px solid #010101; background-color:#fefefe; display:none;">
    <div class="divTitle" id="subTitleId">하위매장</div>
    <div class="divInfoBox" style="line-height:30px; font-size:14px;">
        
        
        <div class="divSearch" id="divSubSearchResult">Total 0</div>
        <table class="default_table">
            <thead>
                <tr>
                    <th><?= lang('Admin.th_no') ?></th>
                    <th><?= lang('Admin.th_level') ?></th>
                    <th><?= lang('Admin.th_uid') ?></th>
                    <th><?= lang('Admin.th_name') ?></th>
                    <th><?= lang('Admin.money_hold') ?></th>
                    <th><?= lang('Admin.point_hold') ?></th>                    
                    <th><?= lang('Admin.th_charge_recall') ?></th>
                    
                </tr>
            </thead>
            <tbody id="tSubbodyList">

            </tbody>
        </table>
    

        <table style="width:100%; border:0px;">
            
            <tbody>
                
                <tr>
                    <td style="border:0px; text-align:center;" colspan="3">
                        <button class="btn_red" onclick="closeSubMember();">닫기</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<div id="divEditSub" style="position:absolute; left:calc(50% - 300px); top:100px; width:600px; border:1px solid #010101; background-color:#fefefe; display:none;">
    <div class="divTitle">총판정보변경</div>
    <div class="divInfoBox" style="line-height:34px; font-size:14px;">
        <input id="editSubNo" type="hidden">
        <table style="width:100%; border:0px;">
            
            <tbody>
                <tr>
                    <td style="width:120px; border:0px;">아이디</td>
                    <td style="width:150px; border:0px;"><span id="editSubId" style="font-weight:bold;"></span></td>
                    <td style="border:0px;"></td>
                </tr>
                <tr>
                    <td style="width:120px; border:0px;">이름</td>
                    <td style="width:150px; border:0px;"><span id="editSubName" style="font-weight:bold;"></span></td>
                    <td style="border:0px;"></td>
                </tr>
                <tr>
                    <td style="width:120px; border:0px;">비밀번호</td>
                    <td style="width:150px; border:0px;"><input type="text" id="editSubPwd" style="width:120px;"></td>
                    <td style="border:0px;"></td>
                </tr>
                <tr>
                    <td style="width:120px; border:0px;">출금 비밀번호</td>
                    <td style="width:150px; border:0px;"><input type="text" id="editSubExcPwd" style="width:120px;"></td>
                    <td style="border:0px;"></td>
                </tr>
                <tr>
                    <td style="width:120px; border:0px;">휴대전화</td>
                    <td style="width:150px; border:0px;"><input type="text" id="editSubPhone" style="width:120px;"></td>
                    <td style="border:0px;"></td>
                </tr>
                <tr>
                    <td style="width:120px; border:0px;">계좌정보</td>
                    <td style="border:0px;" colspan="2">
                        <input type="text" id="editSubBank" style="width:120px;" placeholder="은행명">
                        <input type="text" id="editSubBankNum" style="width:160px;" placeholder="계좌번호">
                        <input type="text" id="editSubBankOwner" style="width:120px;" placeholder="예금주">
                    </td>
                </tr>

                <tr>
                    <td style="width:120px; border:0px;">수수료</td>
                    <td style="width:150px; border:0px;"><input type="text" id="editSubSingleDealRate" style="width:120px;"> %</td>
                    <td style="border:0px;"></td>
                </tr>
                <!--
                <tr>
                    <td style="width:120px; border:0px;">조합 수수료</td>
                    <td style="width:150px; border:0px;"><input type="text" id="editSubMultiDealRate" style="width:120px;"> %</td>
                    <td style="border:0px;"></td>
                </tr>
                <input type="hidden" id="editSubDomain" value="">
                -->
                <tr>
                    <td style="border:0px; text-align:center;" colspan="3">
                        <button class="btn_search" onclick="reqEditMember();">변경</button>&nbsp;
                        <button class="btn_red" onclick="closeEditMember();">닫기</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="divServiceCharge" style="position:absolute; left:calc(50% - 300px); top:150px; width:600px; border:1px solid #010101; background-color:#fefefe; display:none;">
    <div class="divTitle">알충전</div>
    <div class="divInfoBox" style="height:190px; line-height:34px; font-size:14px;">

        <span style="position:absolute; left:10px; top:10px; font-weight:bold;">보내는 사람</span>
        <span id="serviceChargeSenderId" style="position:absolute; left:100px; top:10px; font-weight:bold;"></span>

        <span style="position:absolute; left:calc(50% + 10px); top:10px; font-weight:bold; display:;">보유알</span>
        <span id="serviceChargeSenderMoney" style="position:absolute; left:calc(50% + 100px); top:10px; font-weight:bold; display:;">0</span>

        <input id="serviceChargeRecverEmpid" type="hidden" disabled>
        <input id="serviceChargeRecverUid" type="hidden" disabled>

        <span style="position:absolute; left:10px; top:40px; font-weight:bold;">받는 사람</span>
        <span id="serviceChargeRecverId" style="position:absolute; left:100px; top:40px; font-weight:bold;"></span>

        <span style="position:absolute; left:calc(50% + 10px); top:40px; font-weight:bold;">보유알</span>
        <span id="serviceChargeRecverMoney" style="position:absolute; left:calc(50% + 100px); top:40px; font-weight:bold;">0</span>

        <span style="position:absolute; left:10px; top:70px; font-weight:bold;">충전알</span>
        <input type="text" id="serviceChargeMoney" style="position:absolute; left:100px; top:77px; width:120px;" >

        <div style="position:absolute; left:100px; top:100px;">
            <button class="btn_search" onclick="serviceChargeMoney(10000);">1만원</button>
            <button class="btn_search" onclick="serviceChargeMoney(50000);">5만원</button>
            <button class="btn_search" onclick="serviceChargeMoney(100000);">10만원</button>
            <button class="btn_search" onclick="serviceChargeMoney(500000);">50만원</button>
            <button class="btn_search" onclick="serviceChargeMoney(1000000);">100만원</button>
            <button class="btn_search" onclick="serviceChargeMoney(0);">초기화</button>
        </div>

        <button class="btn_blue" style="position:absolute; left:240px; top:150px;" onclick="reqServiceCharge();">충전</button>&nbsp;
        <button class="btn_red" style="position:absolute; left:300px; top:150px;" onclick="closeServiceCharge();">닫기</button>

    </div>
</div>

<div id="divServiceExchange" style="position:absolute; left:calc(50% - 300px); top:150px; width:600px; border:1px solid #010101; background-color:#fefefe; display:none;">
    <div class="divTitle">알회수</div>
    <div class="divInfoBox" style="height:190px; line-height:34px; font-size:14px;">

        <span style="position:absolute; left:10px; top:10px; font-weight:bold;">받는 사람</span>
        <span id="serviceExchangeRecverId" style="position:absolute; left:100px; top:10px; font-weight:bold;"></span>

        <span style="position:absolute; left:calc(50% + 10px); top:10px; font-weight:bold;">보유알</span>
        <span id="serviceExchangeRecverMoney" style="position:absolute; left:calc(50% + 100px); top:10px; font-weight:bold;">0</span>

        <input id="serviceExchangeSenderEmpid" type="hidden" disabled>
        <input id="serviceExchangeSenderUid" type="hidden" disabled>
        <span style="position:absolute; left:10px; top:40px; font-weight:bold;">보내는 사람</span>
        <span id="serviceExchangeSenderId" style="position:absolute; left:100px; top:40px; font-weight:bold;"></span>

        <span style="position:absolute; left:calc(50% + 10px); top:40px; font-weight:bold;">보유알</span>
        <span id="serviceExchangeSenderMoney" style="position:absolute; left:calc(50% + 100px); top:40px; font-weight:bold;">0</span>

        <span style="position:absolute; left:10px; top:70px; font-weight:bold;">회수알</span>
        <input type="text" id="serviceExchangeMoney" style="position:absolute; left:100px; top:77px; width:120px;">

        <div style="position:absolute; left:100px; top:100px;">
            <button class="btn_search" onclick="serviceExchangeMoney(10000);">1만원</button>
            <button class="btn_search" onclick="serviceExchangeMoney(50000);">5만원</button>
            <button class="btn_search" onclick="serviceExchangeMoney(100000);">10만원</button>
            <button class="btn_search" onclick="serviceExchangeMoney(500000);">50만원</button>
            <button class="btn_search" onclick="serviceExchangeMoney(1000000);">100만원</button>
            <button class="btn_search" onclick="serviceExchangeMoney(0);">초기화</button>
        </div>

        <button class="btn_blue" style="position:absolute; left:240px; top:150px;" onclick="reqServiceExchange();">회수</button>&nbsp;
        <button class="btn_red" style="position:absolute; left:300px; top:150px;" onclick="closeServiceExchange();">닫기</button>

    </div>
</div>




<div id="divEditSubRate" style="position:absolute; left:calc(50% - 300px); top:30px; width:600px; border:1px solid #010101; background-color:#fefefe; display:none;">
	<div class="divTitle" id="divEditSubRateTitle">배당변경</div>
    <div class="divInfoBox" style="line-height:34px; font-size:14px;">
        <input id="editSubRateNo" type="hidden">
        <table class="default_table" style="width:100%; border:0px;">

            <tbody>
                <tr>
                    <td style="width:150px; text-align:center;">파워볼 홀</td>
                    <td>
                        <input id="inputRate_17" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:150px; text-align:center;">파워볼 짝</td>
                    <td>
                        <input id="inputRate_18" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>

                <tr>
                    <td style="width:120px; text-align:center;">파워볼 언더</td>
                    <td>
                        <input id="inputRate_19" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                    <td style="width:120px; text-align:center;">파워볼 오버</td>
                    <td>
                        <input id="inputRate_20" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>

                <tr>
                    <td style="width:120px; text-align:center;">파워볼 홀 + 언더</td>
                    <td>
                        <input id="inputRate_21" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">파워볼 짝 + 언더</td>
                    <td>
                        <input id="inputRate_22" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">파워볼 홀 + 오버</td>
                    <td>
                        <input id="inputRate_23" type="text" style="width:60px; text-align:right;" value="">
                    </td>

                    <td style="width:120px; text-align:center;">파워볼 짝 + 오버</td>
                    <td>
                        <input id="inputRate_24" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>

                <tr>
                    <td style="width:120px; text-align:center;">홀</td>
                    <td>
                        <input id="inputRate_0" type="text" style="width:60px; text-align:right;" value="">
                    </td>

                    <td style="width:120px; text-align:center;">짝</td>
                    <td>
                        <input id="inputRate_1" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">언더</td>
                    <td>
                        <input id="inputRate_2" type="text" style="width:60px; text-align:right;" value="">
                    </td>

                    <td style="width:120px; text-align:center;">오버</td>
                    <td>
                        <input id="inputRate_3" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>

                <tr>
                    <td style="width:120px; text-align:center;">홀 + 언더</td>
                    <td>
                        <input id="inputRate_4" type="text" style="width:60px; text-align:right;" value="">
                    </td>

                    <td style="width:120px; text-align:center;">짝 + 언더</td>
                    <td>
                        <input id="inputRate_5" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">홀 + 오버</td>
                    <td>
                        <input id="inputRate_6" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">짝 + 오버</td>
                    <td>
                        <input id="inputRate_7" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>

                <tr>
                    <td style="width:120px; text-align:center;">대</td>
                    <td>
                        <input id="inputRate_8" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">중</td>
                    <td>
                        <input id="inputRate_9" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">소</td>
                    <td>
                        <input id="inputRate_10" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                    <td style="width:120px; text-align:center;"></td>
                    <td>
                    </td>
                </tr>

                <tr>
                    <td style="width:120px; text-align:center;">홀 + 대</td>
                    <td>
                        <input id="inputRate_11" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">홀 + 중</td>
                    <td>
                        <input id="inputRate_12" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">홀 + 소</td>
                    <td>
                        <input id="inputRate_13" type="text" style="width:60px; text-align:right;" value="">
                    </td>

                    <td style="width:120px; text-align:center;">짝 + 대</td>
                    <td>
                        <input id="inputRate_14" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">짝 + 중</td>
                    <td>
                        <input id="inputRate_15" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">짝 + 소</td>
                    <td>
                        <input id="inputRate_16" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>

                <input id="inputRate_25" type="hidden" value="">
                <input id="inputRate_26" type="hidden" value="">
                <input id="inputRate_27" type="hidden" value="">
                <input id="inputRate_28" type="hidden" value="">
                <input id="inputRate_29" type="hidden" value="">
                <input id="inputRate_30" type="hidden" value="">
                <input id="inputRate_31" type="hidden" value="">
                <input id="inputRate_32" type="hidden" value="">

                <tr>
                    <td style="width:120px; text-align:center;">일반홀 + 언더 + 파워홀</td>
                    <td>
                        <input id="inputRate_33" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">일반홀 + 언더 + 파워짝</td>
                    <td>
                        <input id="inputRate_34" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">일반홀 + 오버 + 파워홀</td>
                    <td>
                        <input id="inputRate_35" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">일반홀 + 오버 + 파워짝</td>
                    <td>
                        <input id="inputRate_36" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">일반짝 + 언더 + 파워홀</td>
                    <td>
                        <input id="inputRate_37" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">일반짝 + 언더 + 파워짝</td>
                    <td>
                        <input id="inputRate_38" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">일반짝 + 오버 + 파워홀</td>
                    <td>
                        <input id="inputRate_39" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">일반짝 + 오버 + 파워짝</td>
                    <td>
                        <input id="inputRate_40" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>

                <tr>
                    <td style="width:120px; text-align:center;">파워볼 0</td>
                    <td>
                        <input id="inputRate_41" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">파워볼 1</td>
                    <td>
                        <input id="inputRate_42" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">파워볼 2</td>
                    <td>
                        <input id="inputRate_43" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">파워볼 3</td>
                    <td>
                        <input id="inputRate_44" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">파워볼 4</td>
                    <td>
                        <input id="inputRate_45" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">파워볼 5</td>
                    <td>
                        <input id="inputRate_46" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">파워볼 6</td>
                    <td>
                        <input id="inputRate_47" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">파워볼 7</td>
                    <td>
                        <input id="inputRate_48" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>
                <tr>
                    <td style="width:120px; text-align:center;">파워볼 8</td>
                    <td>
                        <input id="inputRate_49" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                
                    <td style="width:120px; text-align:center;">파워볼 9</td>
                    <td>
                        <input id="inputRate_50" type="text" style="width:60px; text-align:right;" value="">
                    </td>
                </tr>

                <tr>
                    <td style="border:0px; text-align:center;" colspan="4">
                        <button class="btn_search" onclick="reqEditSubRate();">변경</button>&nbsp;
                        <button class="btn_red" onclick="closeEditSubRate();">닫기</button>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>


<script src="/assets/js/company.js"></script>