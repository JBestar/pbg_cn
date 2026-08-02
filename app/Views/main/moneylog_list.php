

    <div class="divTitle"><?= lang('Admin.title_money_log') ?></div>
    <div class="divSearch">
        <!--
		<form id="formSearch" method="get" action="/Main/money_log">
        -->
			<input type="date" id="inputDateS" value="<?php echo date('Y-m-d'); ?>" name="inputDateS" class="inputDate hasDatepicker">&nbsp;~&nbsp;
            <input type="date" id="inputDateE" value="<?php echo date('Y-m-d'); ?>" name="inputDateE" class="inputDate hasDatepicker">
			&nbsp;&nbsp;
			<select id="selectMoneyType" name="selectMoneyType">
				<option value="0" selected="">통합내역</option>
				<option value="1">충전내역</option>
				<option value="2">환전내역</option>
				<option value="3">알충전</option>
				<option value="4">알회수</option>
				<option value="5">게임구매</option>
				<option value="6">배팅당첨</option>
                <option value="7">구매취소</option>
				<option value="8">포인트적립</option>
				<option value="10">포인트전환</option>
				
			</select>&nbsp;&nbsp;
			<select id="selectSearchType" name="selectSearchType">
				<option value="id" selected="">아이디</option>				
			</select>
			<input type="text" id="inputSearchVal" name="inputSearchVal" class="inputDate" value="">
			<input type="submit" class="btn_search" onclick="reqSearch();" value="<?= lang('Admin.btn_search') ?>" >
			<button class="btn_red" onclick="window.location.reload();"><?= lang('Admin.btn_refresh') ?></button>
		</form>
	</div>
    <div class="divList">
		<table class="default_table">
			<thead>
                <tr>
                    <th><?= lang('Admin.th_no') ?>.</th>
                    <th><?= lang('Admin.th_req_uid') ?></th>
                    <th><?= lang('Admin.th_req_name') ?></th>
                    <th><?= lang('Admin.th_detail') ?></th>
                    <th><?= lang('Admin.th_money_before') ?></th>
                    <th><?= lang('Admin.th_money_change') ?></th>
                    <th><?= lang('Admin.th_money_after') ?></th>
                    <th><?= lang('Admin.th_trade_uid') ?></th>
                    <th><?= lang('Admin.th_change_time') ?></th>
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


<!-- <div class="divContent"> -->    
</div>


<script src="/assets/js/moneylog_list.js"></script>
