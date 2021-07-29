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

if(!empty($_POST['keyword'])) {
    $cha = new Csv();
    $file = $_POST['keyword'];
    if ($file != null) {
		$url = "http://8.130.54.12/api/taobao/v1/pc/search?token=test0303&sort=sale-desc&q=".$file."&page=2";
		//print_r($url);
		$content = file_get_contents($url);
		//var_dump($content);
		if($content != null){
		//json转成数组
		$array = json_decode($content,true);
		//获取data
		$data = $array['data'];
		$item = $data['items'];
		$retuan = array();
		//关键词
		$retuan['keyword'] = $file;
		//获取第四个产品的收货数
		$return['four'] = $item[3]['realSales'];
		
		//获取第四个产品的店铺类型
		//获取最后一个产品的收货数
		$return['last'] = $item[43]['realSales'];
		//获取最后一个产品的店铺类型
		//统计天猫店铺个数
		$userId = true;
		$arr=array_column($item,'isTmall');//把值提取出来转成一维数组
		$arr=array_count_values($arr);//数组的值作为键名，该值在数组中出现的次数作为值
		$return['tmcount'] = $arr[$userId];
		
		}
		var_dump($return);
		die();
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

<form action="/index.php" method ="post">
<div  class="both">
    <div class="left">
	<input name= "keyword" id = "keyword">
	</div>
	 <div class="right"><button id="btnselect" type="button" >查询</button></div>
</div>

</form>
</div>
<script src="https://zb.usoftchina.com/static/js/jquery.min.1.8.3.js"></script>
<script type="text/javascript">
    //ajax提交
    $("#btnselect").click(function(){
        var keyword = $("#keyword").val();
		console.log(keyword);
        if(!keyword){
            alert("关键词不能为空");
        }
       $.post("/index.php",{'keyword':keyword},
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