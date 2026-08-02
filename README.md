# pbg_cn_adm

Copied from `tiger_adm`, DB = `pbg_cn`.

## URL (dedicated port)

```
http://localhost:84/
http://localhost:84/pages/login
```

Apache VirtualHost: port **84** → `pbg_cn_adm/public`  
(게임기 단말은 기존처럼 `http://localhost:83/pbg_cn/public/`)

Optional hosts entry:

```
127.0.0.1  pbg.cn
```

then `http://pbg.cn:84/`

## Seed accounts (password_hash)

| uid | password | level |
|-----|----------|-------|
| admin | admin123 | 9 본사 |
| agency01 | agency123 | 8 총판 |
| store01 | store123 | 7 매장 (기기 로그인용) |

## Language

Admin UI: top-right selector ko / zh / en → saved to `member.mb_lang` (default `ko`).

## Terminal

http://localhost:83/pbg_cn/public/index.html  
Login as store account (e.g. store01 / store123).
"# pbg_cn_adm" 
