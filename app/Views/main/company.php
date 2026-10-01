

    <div class="divTitle"><?= lang('Admin.title_company_mgmt') ?></div>
    <div class="divSearch">
        <!--
		<form name="formSearch" method="get" action="/Main/sub_list">
        <input type="hidden" name="sub_level" value="1">
		-->	
			<input type="date" id="inputDateS" name="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
            <input type="date" id="inputDateE" name="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
			&nbsp;&nbsp;&nbsp;<?= lang('Admin.label_uid') ?> : <input type="text" id="inputSubId" name="inputSubId" class="inputDate" value="" autocomplete="off">
			&nbsp;&nbsp;<?= lang('Admin.label_channel') ?> :
			<select id="selChannel" class="inputDate">
				<option value=""><?= lang('Admin.channel_all') ?></option>
				<option value="0"><?= lang('Admin.channel_cabinet') ?></option>
				<option value="1"><?= lang('Admin.channel_mobile') ?></option>
			</select>
			<button type="button" class="btn_search btn_icon" onclick="reqSearch();" title="<?= lang('Admin.btn_search') ?>" aria-label="<?= lang('Admin.btn_search') ?>"><i class="fas fa-search"></i></button>&nbsp;
            <button type="button" class="btn_reg" style="float:right; margin-right:10px;" onclick="showRegMember();"><?= lang('Admin.title_company_reg') ?></button>
		
	</div>
    <div class="divList">
		<table class="default_table nowrap_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_no') ?></th>
                    <th><?= lang('Admin.th_level') ?></th>
                    <th><?= lang('Admin.th_uid') ?></th>
                    <th><?= lang('Admin.th_name') ?></th>
                    <th><?= lang('Admin.th_password') ?></th>
                    <th><?= lang('Admin.money_hold') ?></th>
                    <th><?= lang('Admin.point_hold') ?></th>
                    <th><?= lang('Admin.th_fee_pct') ?></th>
                    <th><?= lang('Admin.th_store_cnt') ?></th>
                    <th><?= lang('Admin.th_charge_recall') ?></th>
                    <th><?= lang('Admin.th_edit') ?></th>
                    <th><?= lang('Admin.th_delete') ?></th>
                    <th><?= lang('Admin.th_status') ?></th>
                    <th><?= lang('Admin.th_join_date') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList">

            </tbody>
        </table>
	</div>

<style>
@keyframes onlineBlink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.25; }
}
.online-blink {
    display: inline-block;
    padding: 2px 8px;
    background: #f8c8d0;
    color: #c62828;
    font-weight: 700;
    font-size: 12px;
    border-radius: 2px;
    animation: onlineBlink 2s ease-in-out infinite;
}
</style>

    <div id="divRegSub" style="position:absolute; left:calc(50% - 300px); top:100px; width:600px; border:1px solid #010101; background-color:#fefefe; display:none; z-index:2010;">
		<div class="divTitle"><?= lang('Admin.title_company_reg') ?></div>
        <div class="divInfoBox" style="line-height:34px; font-size:14px;">
            <table style="width:100%; border:0px;">
                
                <tbody>
                    <tr>
                        <td style="width:120px; border:0px;"><?= lang('Admin.label_id') ?></td>
                        <td style="width:150px; border:0px;"><input type="text" id="regSubId" style="width:120px;"></td>
                        <td style="border:0px;"><?= lang('Admin.hint_dup_id') ?></td>
                    </tr>
                    <tr>
                        <td style="width:120px; border:0px;"><?= lang('Admin.label_name') ?></td>
                        <td style="width:150px; border:0px;"><input type="text" id="regSubName" style="width:120px;"></td>
                        <td style="border:0px;"><?= lang('Admin.hint_dup_name') ?></td>
                    </tr>
                    <tr>
                        <td style="width:120px; border:0px;"><?= lang('Admin.label_password') ?></td>
                        <td style="width:150px; border:0px;"><input type="password" id="regSubPwd" style="width:120px;"></td>
                        <td style="border:0px;"></td>
                    </tr>
                    <tr>
                        <td style="width:120px; border:0px;"><?= lang('Admin.label_bank_pwd') ?></td>
                        <td style="width:150px; border:0px;"><input type="password" id="regSubExcPwd" style="width:120px;"></td>
                        <td style="border:0px;"></td>
                    </tr>
                    <tr>
                        <td style="width:120px; border:0px;"><?= lang('Admin.label_phone') ?></td>
                        <td style="width:150px; border:0px;"><input type="text" id="regSubPhone" style="width:120px;"></td>
                        <td style="border:0px;"></td>
                    </tr>
                    <tr>
                        <td style="width:120px; border:0px;"><?= lang('Admin.label_bank_info') ?></td>
                        <td style="border:0px;" colspan="2">
                            <input type="text" id="regSubBank" style="width:120px;" placeholder="<?= esc(lang('Admin.ph_bank_name'), 'attr') ?>">
                            <input type="text" id="regSubBankNum" style="width:160px;" placeholder="<?= esc(lang('Admin.ph_bank_num'), 'attr') ?>">
                            <input type="text" id="regSubBankOwner" style="width:120px;" placeholder="<?= esc(lang('Admin.ph_bank_owner'), 'attr') ?>">
                        </td>
                    </tr>

                    <tr>
                        <td style="width:120px; border:0px;"><?= lang('Admin.label_channel') ?></td>
                        <td style="width:150px; border:0px;">
                            <select id="regSubChannel" style="width:126px;">
                                <option value="0"><?= lang('Admin.channel_cabinet') ?></option>
                                <option value="1"><?= lang('Admin.channel_mobile') ?></option>
                            </select>
                        </td>
                        <td style="border:0px;"><?= lang('Admin.hint_channel_fixed') ?></td>
                    </tr>
                    <tr>
                        <td style="width:120px; border:0px;"><?= lang('Admin.label_fee') ?></td>
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
                            <button class="btn_search" onclick="reqRegMember();"><?= lang('Admin.btn_register') ?></button>&nbsp;
                            <button class="btn_red" onclick="closeRegMember();"><?= lang('Admin.btn_close') ?></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<!-- <div class="divContent"> -->    
</div>


<div id="divSubMemberOverlay" class="admin-modal-overlay" style="display:none;" onclick="closeSubMember();"></div>
<div id="divSubMember" class="admin-modal-panel" style="display:none;">
    <div class="divTitle" id="subTitleId"><?= lang('Admin.title_sub_store') ?></div>
    <div class="divInfoBox admin-modal-body" style="line-height:30px; font-size:14px;">
        
        
        <table class="default_table nowrap_table">
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
                        <button type="button" class="btn_red btn_chip" onclick="closeSubMember();"><?= lang('Admin.btn_close') ?></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<div id="divEditSub" style="position:absolute; left:calc(50% - 300px); top:100px; width:600px; border:1px solid #010101; background-color:#fefefe; display:none; z-index:2010;">
    <div class="divTitle"><?= lang('Admin.title_company_edit') ?></div>
    <div class="divInfoBox" style="line-height:34px; font-size:14px;">
        <input id="editSubNo" type="hidden">
        <table style="width:100%; border:0px;">
            
            <tbody>
                <tr>
                    <td style="width:120px; border:0px;"><?= lang('Admin.label_id') ?></td>
                    <td style="width:150px; border:0px;"><span id="editSubId" style="font-weight:bold;"></span></td>
                    <td style="border:0px;"></td>
                </tr>
                <tr>
                    <td style="width:120px; border:0px;"><?= lang('Admin.label_name') ?></td>
                    <td style="width:150px; border:0px;"><span id="editSubName" style="font-weight:bold;"></span></td>
                    <td style="border:0px;"></td>
                </tr>
                <tr>
                    <td style="width:120px; border:0px;"><?= lang('Admin.label_channel') ?></td>
                    <td style="width:150px; border:0px;"><span id="editSubChannel" style="font-weight:bold;"></span></td>
                    <td style="border:0px;"><?= lang('Admin.hint_channel_fixed') ?></td>
                </tr>
                <tr>
                    <td style="width:120px; border:0px;"><?= lang('Admin.label_password') ?></td>
                    <td style="width:150px; border:0px;"><input type="text" id="editSubPwd" style="width:120px;"></td>
                    <td style="border:0px;"></td>
                </tr>
                <tr>
                    <td style="width:120px; border:0px;"><?= lang('Admin.label_bank_pwd') ?></td>
                    <td style="width:150px; border:0px;"><input type="text" id="editSubExcPwd" style="width:120px;"></td>
                    <td style="border:0px;"></td>
                </tr>
                <tr>
                    <td style="width:120px; border:0px;"><?= lang('Admin.label_phone') ?></td>
                    <td style="width:150px; border:0px;"><input type="text" id="editSubPhone" style="width:120px;"></td>
                    <td style="border:0px;"></td>
                </tr>
                <tr>
                    <td style="width:120px; border:0px;"><?= lang('Admin.label_bank_info') ?></td>
                    <td style="border:0px;" colspan="2">
                        <input type="text" id="editSubBank" style="width:120px;" placeholder="<?= esc(lang('Admin.ph_bank_name'), 'attr') ?>">
                        <input type="text" id="editSubBankNum" style="width:160px;" placeholder="<?= esc(lang('Admin.ph_bank_num'), 'attr') ?>">
                        <input type="text" id="editSubBankOwner" style="width:120px;" placeholder="<?= esc(lang('Admin.ph_bank_owner'), 'attr') ?>">
                    </td>
                </tr>

                <tr>
                    <td style="width:120px; border:0px;"><?= lang('Admin.label_fee') ?></td>
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
                        <button class="btn_search" onclick="reqEditMember();"><?= lang('Admin.btn_change') ?></button>&nbsp;
                        <button class="btn_red" onclick="closeEditMember();"><?= lang('Admin.btn_close') ?></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="divServiceCharge" style="position:fixed; left:calc(50% - 300px); top:120px; width:600px; border:1px solid #010101; background-color:#fefefe; display:none; z-index:2010;">
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

        <button class="btn_blue" style="position:absolute; left:240px; top:150px;" onclick="reqServiceCharge();"><?= lang('Admin.btn_egg_charge') ?></button>&nbsp;
        <button class="btn_red" style="position:absolute; left:300px; top:150px;" onclick="closeServiceCharge();"><?= lang('Admin.btn_close') ?></button>

    </div>
</div>

<div id="divServiceExchange" style="position:fixed; left:calc(50% - 300px); top:120px; width:600px; border:1px solid #010101; background-color:#fefefe; display:none; z-index:2010;">
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

        <button class="btn_blue" style="position:absolute; left:240px; top:150px;" onclick="reqServiceExchange();"><?= lang('Admin.btn_egg_recover') ?></button>&nbsp;
        <button class="btn_red" style="position:absolute; left:300px; top:150px;" onclick="closeServiceExchange();"><?= lang('Admin.btn_close') ?></button>

    </div>
</div>




<div id="divEditSubRate" style="position:absolute; left:calc(50% - 300px); top:30px; width:600px; border:1px solid #010101; background-color:#fefefe; display:none; z-index:2010;">
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


<script>
window.ADMIN_I18N = window.ADMIN_I18N || {};
window.ADMIN_I18N.btn_egg_charge = <?= json_encode(lang('Admin.btn_egg_charge'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_egg_recover = <?= json_encode(lang('Admin.btn_egg_recover'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_edit = <?= json_encode(lang('Admin.btn_edit'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_approve = <?= json_encode(lang('Admin.btn_approve'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_block = <?= json_encode(lang('Admin.btn_block'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.btn_delete = <?= json_encode(lang('Admin.btn_delete'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.status_online = <?= json_encode(lang('Admin.status_online'), JSON_UNESCAPED_UNICODE) ?>;
window.ADMIN_I18N.title_sub_store_of = <?= json_encode(lang('Admin.title_sub_store_of'), JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="/assets/js/company.js?v=9"></script>