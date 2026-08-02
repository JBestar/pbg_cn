<!doctype html>
<html>
	<head>
        <meta content="IE=11.0000" http-equiv="X-UA-Compatible">
        <meta charset="utf-8">
	    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		
		<title><?=$site_name?> <?= lang('Admin.login_title') ?></title>

		<link rel="shortcut icon" type="image/png" href="/favicon.ico"/>
        <link rel="stylesheet" href="/assets/css/lib/all.css">
        <link rel="stylesheet" href="/assets/css/button1.css">
        <link rel="stylesheet" href="/assets/css/button2.css">
        <link rel="stylesheet" href="/assets/css/layer.css">
        <!-- JQuery 1.12.4--> 
	    <script src="/assets/js/lib/jquery-1.12.4.min.js"></script>
        <script src="/assets/js/lib/fontawesome.js"></script>
        <script src="/assets/js/util.js?V=1"></script>
    </head>

    <body>
                
        <style>
            body {
                background: #FFF url('/assets/img/login_bg1.jpg') no-repeat center center fixed; 
                -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;
            }

            .input_login {
                width:228px;
                height:30px;
                border: 1px solid #777777;
                margin-bottom:8px;
                padding-left:10px;
                padding-right:10px;
            }
            .tit {
                line-height: 0px;
                font-size: 80px;
                font-weight: 1000;
                color: #fff600;
                text-align:center;
                text-shadow: 2px 2px 2px #444;
                /* -webkit-text-stroke: 1px white; */
                font-family: Times New Roman, sans-serif, Arial;
            }
        </style>
        <div style="position:absolute; left:calc(50% - 125px); top:calc(50% - 350px); margin: auto; width:250px; border:0px solid red;">
            <p class="tit">PBG</p>
            <input name="user_id" class="input_login" placeholder="<?= lang('Admin.login_id') ?>" maxlength="50" type="text" id="user_id" value="">
            <input name="user_pw" class="input_login" placeholder="<?= lang('Admin.login_pwd') ?>" maxlength="50" type="password" id="user_pw" value="">
            <img id="image_id" name="<?=$captcha?>" src="/download/captcha/<?=$captcha?>.jpg" style="width: 249px; height: 30px; border: 0;" alt=" ">
            <input name="captcha_word" class="input_login" placeholder="보안문자" maxlength="50" type="text" id="captcha_word">
            <button type="button" class="btn btn-info" style="width:250px;" onclick="login();"><?= lang('Admin.login_btn') ?></button>
        </div>
        <div class="layui-layer-shade" id="layui-layer-shade" times="1" style=" display:none; z-index:90000; background-color:#000; opacity:0.3; filter:alpha(opacity=30);">
        </div>
        <div class="layui-layer layui-layer-dialog layer-anim" id="layui-layer" type="dialog" times="1" showtime="0" contype="string" 
                            style=" display:none; z-index: 91000; left:calc(50% - 130px); top:calc(50% - 80px); ">
            <div class="layui-layer-title" style="cursor: move;">파워볼</div>
            <div class="layui-layer-content">
                <div id="layui-layer-msg" style="color: #000000; text-align: center;"></div>
            </div>
            <span class="layui-layer-setwin"><a class="layui-layer-ico layui-layer-close layui-layer-close1" href="javascript:closeAlert();"></a></span>
            <div class="layui-layer-btn layui-layer-btn-"><a class="layui-layer-btn0"  href="javascript:closeAlert();">확인</a></div>
            <span class="layui-layer-resize"></span>
        </div>     

    </body>
    <script>
        function onEnter()
        {
            if( window.event.keyCode == 13 ) login();
        }
        
        function login()
        {
            var strId = $('#user_id').val();
            var strPwd = $('#user_pw').val();
            var strCaptcha = $('#captcha_word').val();
            var strImage = $('#image_id').attr('name');

            if( strId.length == 0 )
            {
                showAlert('아이디를 입력해주세요');
                return ;
            }

            if( strPwd.length == 0 )
            {
                showAlert('비밀번호를 입력해주세요');
                return ;
            }

            if( strCaptcha.length == 0 )
            {
                showAlert('보안문자를 입력해주세요');
                return ;
            }

            var objData = { "uid":strId, "pwd":strPwd, "captcha":strCaptcha, "img":strImage};
            var jsonData = JSON.stringify(objData);
            
            $.ajax({
                type: "POST",
                dataType: "json",
                url:"/api/login",
                data: {"json_":jsonData},
                success: function(jResult) {
                    // console.log(jResult);
                    if(jResult.status == "success")
                    {
                        setCookie('logged', 'yes', 0);
                        location.replace("/");
                    }
                    else if(jResult.status == "fail")
                    {    
                        if(jResult.code == 3)
                            showAlert('차단된 아이디입니다.');
                        else if(jResult.code == 6)
                            showAlert('사용중인 아이디입니다.');   
                        else if(jResult.code == 8)
                            showAlert('사이트 점검 중입니다.');   
                        else if(jResult.code == 11)
                            showAlert('보안문자가 틀림니다');
                        else if(jResult.code == 12)
                            showAlert('보안문자가 만료되었습니다. 다시 입력해주세요');
                        else 
                            showAlert('아이디, 비밀번호를 확인해주세요');
                        
                    }
                },  
                error:function(request,status,error){
                    console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                    showAlert('로그인 요청이 실패했습니다. 잠시 후 다시 시도해주세요');
                }
            });

        }

    </script>

</html>