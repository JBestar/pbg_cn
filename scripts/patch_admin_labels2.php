<?php
/**
 * Patch remaining admin view labels (titles + filter 아이디/수신자).
 */
$dir = 'D:/xampp/htdocs/pbg_cn_adm/app/Views/main';
$map = [
    '<div class="divTitle">충전관리</div>' => '<div class="divTitle"><?= lang(\'Admin.title_charge_mgmt\') ?></div>',
    '<div class="divTitle">환전관리</div>' => '<div class="divTitle"><?= lang(\'Admin.title_exchange_mgmt\') ?></div>',
    '<div class="divTitle">매장관리</div>' => '<div class="divTitle"><?= lang(\'Admin.title_agency_mgmt\') ?></div>',
    '<div class="divTitle">총판관리</div>' => '<div class="divTitle"><?= lang(\'Admin.title_company_mgmt\') ?></div>',
    '<div class="divTitle">쪽지관리</div>' => '<div class="divTitle"><?= lang(\'Admin.title_memo_mgmt\') ?></div>',
    '<div class="divTitle">문의관리</div>' => '<div class="divTitle"><?= lang(\'Admin.title_qna_mgmt\') ?></div>',
    '<div class="divTitle">머니변동내역</div>' => '<div class="divTitle"><?= lang(\'Admin.title_money_log\') ?></div>',
    '<div class="divTitle">설정</div>' => '<div class="divTitle"><?= lang(\'Admin.title_setting\') ?></div>',
    '아이디 : <input' => '<?= lang(\'Admin.label_uid\') ?> : <input',
    '수신자 : <input' => '<?= lang(\'Admin.label_receiver\') ?> : <input',
    '>매장등록</button>' => '><?= lang(\'Admin.title_agency_reg\') ?></button>',
    '>총판등록</button>' => '><?= lang(\'Admin.title_company_reg\') ?></button>',
];

foreach (glob($dir . '/*.php') as $file) {
    $src = file_get_contents($file);
    $out = str_replace(array_keys($map), array_values($map), $src);
    if ($out !== $src) {
        file_put_contents($file, $out);
        echo 'patched ' . basename($file) . "\n";
    }
}
echo "done\n";
