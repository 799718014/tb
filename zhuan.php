<?php
/**
 * Created by PhpStorm.
 * User: my
 * Date: 2019/2/19
 * Time: 11:42
 */
class Csv
{
    /**
     * 读取CSV文件
     * @param string $csv_file csv文件路径
     * @param int $lines       读取行数
     * @param int $offset      起始行数
     * @return array|bool
     */
    public function read_csv_lines($fname = '')
    {
		//获取文件的编码方式
        $contents = file_get_contents($fname);
        $encoding = mb_detect_encoding($contents, array('GB2312','GBK','UTF-16','UCS-2','UTF-8','BIG5','ASCII'));
        $fp=fopen($fname,"r");//以只读的方式打开文件
		$text = "";
		$num = 0;
		if(!(feof($fp))) {

		  $num++;

		  $str = trim(fgets($fp));

		  if ($encoding != false) {

			  $str = iconv($encoding, 'UTF-8', $str);

			  if ($str != "" and $str != NULL) {

				  $text = $str;

			  }

		  }else {

			  $str = mb_convert_encoding ( $str, 'UTF-8','Unicode');

			  if ($str != "" and $str != NULL) {

				  $text = $str;

			  }

		  }

		}

		while(!(feof($fp))) {
		  $str = '';
		  $str = trim(fgets($fp));
		  if ($encoding != false) {
			  $str = iconv($encoding, 'UTF-8', $str);
			  if ($str != "" and $str != NULL) {
				  $text = $text."##".$str;
			  }
		  }else {
			  $str = mb_convert_encoding ( $str, 'UTF-8','Unicode');
			  if ($str != "" and $str != NULL) {
				$text = $text."##".$str;
			  }
		  }
		}   
        fclose($fp);
		return $text;
    }

    //转换代码块，把数组转成可发布字符串
    public function  str_to_str($data){
        $string = '';
        $string2 = '';
		//字符串分割成数组
		$arr = explode("##",$data);
        //第一层循环
        foreach ($arr as $k=>$v){
			//取余。有余数产品词，没有余数蓝海词
			if($k%2 === 0){
				//echo $v;
				$string .= $v. "\r\n";
			}else{
				$string2 .= $v. "\r\n";
			}
        }
        return array('blue'=>$string,'keyw'=>$string2);
    }
}

if(!empty($_POST['file'])) {
    $cha = new Csv();
    $file = $_POST['file'];
    $fname = "C:/Users/my/Desktop/chanpin/".$file;
    //$fname = "C:/Users/Administrator/Desktop/chanpin/".$file;
    if (file_exists($fname)) {
        $text = $cha->read_csv_lines($fname);
        //字符串转数组
        $str = $cha->str_to_str($text);
        echo json_encode($str);
        die();
    } else {
	    echo "文件不存在";
        die();
    }
}   
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>过滤出蓝海词和产品词</title>
    <style type="text/css">
        .left{float:left;}
        .both{clear: both;  width:100%;}
        .long{width: 340px;}
        .box{border: 1px solid #999;}
		.textwith{
			margin: 0px;
            height: 320px;
            width: 330px;
        }
        .titleboth{border: 1px #000 solid;width: 1040px;height: 20px;background-color: #0b0b0b;color: #fff;}
    </style>
</head>
<body>
<div class=" titleboth box">
<div  class="titleboth top">
    <div class="left long">产品词</div>
    <div class="left long">蓝海词</div>
    <div class="left long">文件名</div>
</div>
<form action="/zhuan.php" method ="post">
<div  class="both">
    <div class="left"><textarea id="keyw"  class="textwith" placeholder="生成产品词"></textarea>
	 <div class="right"><button class="copyname1" type="button">一键复制</button></div>
	</div>
    <div class="left"><textarea id="blue" class="textwith" class="textwith" placeholder="生成蓝海词"></textarea>
	 <div class="right"><button class="copyname2" type="button">一键复制</button></div>
	</div>
    <div class="left"><textarea id="file" class="textwith" name="file" class="textwith" placeholder="需要处理的文件名"></textarea></div>>
	 <div class="right"><button id="btnselect" type="button" style="width: 200px;height:40px;float: left; ">查询</button></div>
</div>

</form>
</div>
<script src="https://zb.usoftchina.com/static/js/jquery.min.1.8.3.js"></script>
<script type="text/javascript">
    //ajax提交
    $("#btnselect").click(function(){
        var file = $("#file").val();
        if(!file){
            alert("文件名不能为空");
        }
       $.post("/zhuan.php",{'file':file},
            function(val,status){
               if(val != '文件不存在'){
                   var obj = JSON.parse(val);
                   $('#keyw').text(obj.keyw);
                   $('#blue').text(obj.blue);
               }else{
                   alert("文件名不存在");
               }
            });
    });

    //一键复制 产品词
    $('.copyname1').click(function(){
        var dd = $('#keyw').select();
        document.execCommand("Copy");
    });
    //blue
    $('.copyname2').click(function(){
        var dd = $('#blue').select();
        document.execCommand("Copy");
    });

   
</script>
</body>

</html>