<?php
    // PS：新旧文件名修改路径根据系统决定
    // 如Windows使用相对路径和绝对路径都可以
    // Linux经测试修改时不可使用相对路径
    // 且Linux的http用户权限必须有写入文件和删除文件权限
    $HTTP_REFERER = $_SERVER['HTTP_REFERER'];   //获取请求信息
    if (isset($HTTP_REFERER)) {
        
        if (strstr($HTTP_REFERER,'mrhao')) {
            echo 'ok';
        }else{
            echo ('<h1>非法访问1</h1>');
            exit();
        }
    }else { 
        echo ('<h1>非法访问2</h1>');
        exit();
    }
    $time = time(); //获取时间戳

    $getfile = $_GET[file];    //获取参数file

    $base64de = base64_decode($getfile);   //解密file参数

    $dname = md5("$base64de.$getdlfile.$time");    //拼接解密的文件名+参数file+现在时间戳，并MD5加密留用

    $filecz = file_exists("./$base64de");   //判断请求文件是否存在

    $data = array('time' => "$time",'yname' => "$base64de",'dname' => "$dname.zip");    //更新数组数据留用

    $datajson =  json_encode($data);    //格式化数组数据为json

    if ($filecz==true) {    //首次上传文件修改
        $fileput = file_put_contents ("./ZmlsZQ==/$base64de".'.php', "$datajson"); //写入数组到文件
        $oldname = "/volume1/web/owncloud/$base64de";   //旧名字（首次上传名）
        $newname = "/volume1/web/owncloud/$dname.zip";  //新名字（自动根据时间戳生成）
        rename($oldname,$newname);    //修改名字
    }

    $fileget = file_get_contents ("./ZmlsZQ==/$base64de".'.php');  //读取现存数据

    $datajsonde = json_decode($fileget);    //解json现存数据

    $filetime = $datajsonde->{'time'}; //提取现存数据时间戳

    $filename = $datajsonde->{'dname'}; //提取现存文件识标

    $timeadd = $filetime + 20;   //设置过期时间

    if ($filetime == null) {    //如果获取不到现存数据直接停止
        echo '验证下载失败，请联系管理员';
        exit();
    }
    
    if ($timeadd < $time) {     //如果过期就将上方的数组写入
        echo '获取链接中';
        $oldname = "/volume1/web/owncloud/$filename"; //旧名字（现存数据读取）
        $newname = "/volume1/web/owncloud/$dname.zip";  //新名字（自动根据时间戳生成）
        rename($oldname,$newname);   //重新命名文件以更新链接
        $fileput = file_put_contents ("./ZmlsZQ==/$base64de".'.php', "$datajson");     //写入数据以便下次读取
        //header("Refresh:0");    //自动刷新以不动声色下载
        echo 'ok';
        echo '<script>'; 
		echo "window.location.href='check.php?file=$getfile'"; 
		echo '</script>';
    }else{
        echo '可下载';
        echo "<script>"; 
		echo "window.location.href='$filename'"; 
		echo "</script>";
        //header("Location: $dname.zip");  //若在时限内则可直接访问下载
    }
?>