<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>天气预报</title>
	<link rel="stylesheet" type="text/css" href="about/base.css">
	<link rel="stylesheet" type="text/css" href="about/weather.css">
	<script type="text/javascript" src="about/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="about/echarts.min.js"></script>
</head>
	<?php
		error_reporting(0);

		function microtime_float(){
			list($usec, $sec) = explode(' ', microtime());
			return ((float)$usec + (float)$sec);
		}

		function setEnv($envFile){
			$env = file($envFile, FILE_IGNORE_NEW_LINES);
			foreach($env as $line){
				putenv($line);
			}
		}

		function getCity($cityFile){
			try{
			$cityarr = file($cityFile, FILE_IGNORE_NEW_LINES);
				if(empty($cityarr)){
					throw new Exception('city is empty!');
				}
			} catch(Exception $e){
				print 'error: '.$e->getMessage();
				exit;
			}
			return $cityarr;
		}

		setEnv('about/config.sh');
		$cities = getCity('about/city.ini');
		$chooseJson = array();

		// $timestart = microtime_float();
		foreach ($cities as $city)
		{
			// $ctimestart = microtime_float();
			list($cityid, $cityname) = explode("=", $city);
			$ch = curl_init();
			$url = 'http://apis.baidu.com/heweather/weather/free?cityid='.$cityid;
			$header = array('apikey:'.getenv('BAIDUAPIKEY'),);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			$res = curl_exec($ch);
			$jdata = json_decode($res, true);
			array_push($chooseJson, $jdata);
			// $ctiemend = microtime_float();
			// echo 'city - '.$cityid.' elapsed: '.($ctiemend - $ctimestart).'<br>';
		}
		// echo 'all cities elapsed: '.(microtime_float() - $timestart).'<br>';
	?>
<style>
	header{height:80px;border-bottom:2px solid #666;}
	header .center{width:1000px;margin:0 auto;height:80px;}
	header .center span{font-size:14px;margin-left:30px;font-weight:normal;}
	header .center span:first-child{font-size:20px;font-weight:bold;line-height:80px;margin-left:0;}
	.weather-list{}
	.weather-list .page-center{width:1200px;margin:0 auto;}
	.weather-list .page-center li{float:left;width:400px;margin-left: 100px;}
	.weather-list .page-center li h1{height:60px;line-height:60px;font-size:18px;position:relative;}
	.weather-list .page-center li h1 a{color:blue;font-size:14px;float:right;font-weight:bold;}
	.weather-list .page-center li h1 .icon{height:25px;width:25px;position:absolute;top:50%;transform: translatey(-50%);-webkit-transform: translatey(-50%);left:80px;}
	.weather-list .page-center li h1 .icon img{position:absolute;height:25px;width:25px;}
	.weather-list .page-center li h1 .state{margin-left:90px;}
	.weather-list .page-center li ol{border-top:1px solid #ccc;border-right:1px solid #ccc;overflow:hidden;background:#f5f5f5;}
	.weather-list .page-center li ol li{overflow:hidden;float:none;margin-left:0;}
	.weather-list .page-center li ol li div{float:left;box-sizing: border-box;width:14.2%;border-left:1px solid #ccc;font-size:12px;text-align:center;position:relative;border-bottom:1px solid #ccc;}
	.weather-list .page-center li ol li div:first-child{background:#fff;}
	.weather-list .page-center li ol li.date-line div{line-height:30px;height:30px;}
	.weather-list .page-center li ol li.table-icon div{line-height:45px;height:45px;}
	.weather-list .page-center li ol li.table-icon div img{width:30px;position:absolute;left:50%;top:50%;transform: translate(-50%,-50%);-webkit-transform: translate(-50%,-50%);}
	.weather-list .page-center li ol li.table-state div{height:30px;line-height:30px;}
	/* .weather-list .page-center li ol li.Line-graph {border-left:1px solid #ccc;border-bottom:1px solid #ccc;} */
	.weather-list .page-center li ol li.Line-graph div{height:70px;}
	.weather-list .page-center li ol li.night-iocn div{line-height:45px;height:45px;}
	.weather-list .page-center li ol li.night-iocn div img{width:30px;position:absolute;left:50%;top:50%;transform: translate(-50%,-50%);}
	.weather-list .page-center li ol li.night-state div{height:30px;line-height:30px;}
	#main{border:none;}

</style>
<script type="text/javascript">
	$(function() {
		var myDate = new Date();
		var myMonth = myDate.getMonth() + 1;
		var myDay = myDate.getDate();

		$('.center span').eq(0).html(myMonth+'月'+myDay+'日天气预报')
	});
</script>
<body>
	<header>
		<div class="center"><span>5月17日天气预报</span>
			<span>最后更新：
				<?php
			        echo $chooseJson[0]["HeWeather data service 3.0"][0]["basic"]["update"]["loc"];
			    ?>
		    </span>
		</div>
	</header>
	<section class="weather-list">
		<div class="page-center">
			<ul>
				<?php foreach ($chooseJson as $val){ ?>
					<li class="onecity">
					<h1><span class="city">
							<span>
								<?php echo $val["HeWeather data service 3.0"][0]["basic"]["city"]; ?>
							</span>
						</span> 
					<span class="icon">
							<img src="images/<?php echo $val["HeWeather data service 3.0"][0]["now"]["cond"]["code"]; ?>.png" alt="">
					</span>
						<span class="state">
							<?php echo $val["HeWeather data service 3.0"][0]["now"]["cond"]["txt"];?>&nbsp;&nbsp;
							<?php echo $val["HeWeather data service 3.0"][0]["now"]["tmp"]; ?>°C
					</span></h1>
					<ol>
						<li class="date-line">
							<?php foreach ( $val["HeWeather data service 3.0"][0]["daily_forecast"] as $valsmall){
												$mydate = substr($valsmall["date"], 5);
							    	echo  '<div>'.$mydate.'</div>';
								} ?>
						</li>
						<li class="table-icon">
							<?php foreach ($val["HeWeather data service 3.0"][0]["daily_forecast"] as $valsmall){
								$myimg = $valsmall["cond"]["code_d"];
								echo  '<div><img src="images/'.$myimg.'.png" alt=""></div>';
						    } ?>
						</li>
						<li class="table-state">
							<?php foreach ($val["HeWeather data service 3.0"][0]["daily_forecast"] as $valsmall){
							    echo  '<div>'.$valsmall["cond"]["txt_d"].'</div>';
							} ?>
						</li>
						<li class="Line-graph">
							<div id="<?php echo $val["HeWeather data service 3.0"][0]["basic"]["id"]; ?>" style="height:130px;width:400px;background-color:#f5f5f5;" class="tubiao"></div>
						</li>
					</ol>
						<div class="weektep" style="display:none;">
							<?php foreach ($val["HeWeather data service 3.0"][0]["daily_forecast"] as $valsmall){
									echo  '<span>'.$valsmall["tmp"]["max"].'</span>';
								} ?>
						</div>
					</li>
				<?php } ?>
			</ul>
		</div>


	</section>

</body>
</html>
	<script type="text/javascript">

 $('.onecity').each(function (index) {
        var mayarr = new Array()

    for (var i = 0; i < 7; i++){
		var member=$(this).find('.weektep span').eq(i).text();
    	mayarr.push(member)
	
		};
var thisid=$(this).find('.Line-graph div').attr('id');

var myChart = echarts.init(document.getElementById(thisid));

    option = {

   	grid:  {
        top: 20,
        left : 27,
        right : 27,
        bottom : 15,

    },
    xAxis:  {
        type: 'category',
        show : false,
        boundaryGap: false,
        data: ['第一天','第二天','第三天','第四天','第五天','第六天','第七天']
    },
    yAxis: {
        type: 'value',
         show : false,
        axisLabel: {
            formatter: '{value} °C'
        }
    },
    series: [
		        {
		            name:'日温',
		            type:'line',

		            data:mayarr,
		            itemStyle : { normal: {label : {show: true,
		            								  formatter: "{c}°C"
		            								}

						        		  }
						    	},

		           
		        }    
        	]
};
        myChart.setOption(option);

})


//     })

//     var myChart = echarts.init(document.getElementById('main'));
//     var mayarr = new Array()
//     for (var i = 0; i < 7; i++) {
//     	member=$('#weektep span').eq(i).text();
//     	mayarr.push(member)
    	

//     };
//     option = {

//    	grid:  {
//         top: 20,
//         left : 27,
//         right : 27,
//         bottom : 15,

//     },
//     xAxis:  {
//         type: 'category',
//         show : false,
//         boundaryGap: false,
//         data: ['第一天','第二天','第三天','第四天','第五天','第六天','第七天']
//     },
//     yAxis: {
//         type: 'value',
//          show : false,
//         axisLabel: {
//             formatter: '{value} °C'
//         }
//     },
//     series: [
// 		        {
// 		            name:'日温',
// 		            type:'line',

// 		            data:mayarr,
// 		            itemStyle : { normal: {label : {show: true,
// 		            								  formatter: "{c}°C"
// 		            								}

// 						        		  }
// 						    	},

// 		        }
//         	]
// };
//         myChart.setOption(option);

</script>