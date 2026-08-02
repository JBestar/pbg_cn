

    <div class="divTitle"><?= lang('Admin.title_charge_mgmt') ?></div>
    <div class="divSearch">
        <!--
		<form name="formSearch" method="get" action="/Main/charge_list">
        -->	
        <input type="date" id="inputDateS" name="inputDateS" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">&nbsp;~&nbsp;
        <input type="date" id="inputDateE" name="inputDateE" value="<?php echo date('Y-m-d'); ?>" class="inputDate hasDatepicker">
			&nbsp;&nbsp;&nbsp;<?= lang('Admin.label_uid') ?> : <input type="text" id="inputSubId" class="inputSubId" value="">
			<input type="submit" class="btn_search" onclick="reqSearch();" value="<?= lang('Admin.btn_search') ?>">&nbsp;
            <button class="btn_red" onclick="location.reload();"><?= lang('Admin.btn_refresh') ?></button>
		
	</div>
    
    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>   
                    <th><?= lang('Admin.th_charge_type') ?></th>                 
                    <th><?= lang('Admin.th_charge_amount') ?></th>
                    <th><?= lang('Admin.th_level') ?></th>
                    <th><?= lang('Admin.th_uid') ?></th>
                    <th><?= lang('Admin.th_name') ?></th>
                    <th><?= lang('Admin.th_depositor_short') ?></th>
                    <th><?= lang('Admin.th_req_datetime') ?></th>
                    <th><?= lang('Admin.th_proc_datetime') ?></th>
                    <th><?= lang('Admin.th_status') ?></th>
                    <th><?= lang('Admin.th_process') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList">
                <!--
                <tr>
                    <td class="tdMoney">2,000,000</td>
                    <td class="tdDate">여수1</td>
                    <td class="tdDate">여수1</td>
                    <td class="tdDate">1</td>
                    <td class="tdDate">2021-10-17 23:24:51.580</td>
                    <td class="tdDate">2021-10-17 23:29:26.000</td>
                    <td class="tdDate">완료</td>
                    <td class="tdDate">
                        <button type="button" class="btn_blue" onclick="deleteCharge(90114);">확인</button>
                        <button type="button" class="btn_red" onclick="deleteCharge(90114);">취소</button>
                    </td>
                </tr>
                
                <tr>                    
                    <td class="tdMoney">2,000,000</td>
                    <td class="tdDate">여수1</td>
                    <td class="tdDate">여수1</td>
                    <td class="tdDate">mbc&nbsp;/&nbsp;여수</td>
                    <td class="tdDate">1</td>
                    <td class="tdDate">2021-10-17 23:17:35.140</td>
                    <td class="tdDate">2021-10-17 23:18:17.000</td>
                    <td class="tdDate">완료</td>
                    <td class="tdDate">
                        <button type="button" class="btn_red" onclick="deleteCharge(90114);">삭제</button>
                    </td>
                </tr>
                -->
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

    <!-- <div class="divContent"> -->    
    </div>


    <script src="/assets/js/charge_list.js"></script>