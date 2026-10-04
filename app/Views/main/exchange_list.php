
    <div class="divTitle"><?= lang('Admin.title_store_exchange_req') ?></div>
    <?= view('main/sub_req_style') ?>
    <div class="divSearch">
        <?= lang('Admin.label_period') ?> :
        <input type="text" id="inputDateS" name="inputDateS" value="" placeholder="" autocomplete="off" class="inputDate"
               onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">&nbsp;~&nbsp;
        <input type="text" id="inputDateE" name="inputDateE" value="" placeholder="" autocomplete="off" class="inputDate"
               onfocus="this.type='date'" onblur="if(!this.value)this.type='text'">
        &nbsp;&nbsp;&nbsp;<?= lang('Admin.label_uid') ?> : <input type="text" id="inputSubId" class="inputSubId" value="">
        <button type="button" class="btn_search btn_icon" onclick="reqSearch();" title="<?= lang('Admin.btn_search') ?>" aria-label="<?= lang('Admin.btn_search') ?>"><i class="fas fa-search"></i></button>&nbsp;
        <button type="button" class="btn_red" onclick="window.location.reload();"><?= lang('Admin.btn_refresh') ?></button>
    </div>

    <div class="divList">
        <table class="default_table sub-req-table">
            <thead>
                <tr>
                    <th><?= lang('Admin.th_uid') ?></th>
                    <th><?= lang('Admin.th_name') ?></th>
                    <th><?= lang('Admin.th_req_amount') ?></th>
                    <th><?= lang('Admin.th_status') ?></th>
                    <th><?= lang('Admin.th_confirm') ?></th>
                    <th><?= lang('Admin.th_exchange_type') ?></th>
                    <th><?= lang('Admin.th_apply_datetime') ?></th>
                    <th><?= lang('Admin.th_proc_datetime') ?></th>
                </tr>
            </thead>
            <tbody id="tbodyList">
            </tbody>
            <tfoot>
                <tr class="sub-req-sum">
                    <td colspan="2"><?= lang('Admin.th_total') ?></td>
                    <td class="tdMoney" id="tdSumMoney">0</td>
                    <td colspan="5"></td>
                </tr>
            </tfoot>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td id="tdPageNation" align="center" height="50">
                        <a class="light-theme" id="aPagePrev" href="javascript:prevPage();"><span class="light-theme"><<</span></a>
                        <span id="spanPaginationNum"></span>
                        <a class="light-theme" id="aPageNext" href="javascript:nextPage();"><span class="light-theme">>></span></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<?= view('main/sub_req_i18n', array('sub_req_kind' => 'exchange')) ?>
<script src="/assets/js/sub_req_list.js?v=1"></script>
