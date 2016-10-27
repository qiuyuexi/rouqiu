<?php 
/**生成验证码图像
 * 
 */
class verify{

	private $config;//基本配置
	
	private $img;//生成的图片
	
	private $bg_color;//图片背景颜色
	
	private $text_color;//文字颜色

	private $verify;//生成的验证码

	private $path;//图片保存的地址

	public function __construct(){

		$this->path = '/var/www/verify/'.PROJECT;
		
		if(!is_dir($this->path)){
		
			mkdir($this->path,0755,true);
		}
		
		$this->config = array(
		
			'x_size'=>100,//宽
		
			'y_size'=>30,//长
		
			'bg_r'=>255,// 设置图片背景颜色，RGB 成分组成的颜色，red，green 和 blue 分别是所需要的颜色的红，绿，蓝成分
		
			'bg_g'=>255,
		
			'bg_b'=>255,
		);
		
		$this->img = imagecreate($this->config['x_size'],$this->config['y_size'] );//创建一个空白的图片
		
		$this->bg_color = imagecolorallocate($this->img,$this->config['bg_r'],$this->config['bg_g'],$this->config['bg_b']);//设置图片的背景色 ,
	}


	/**
	 * [getVerify description]
	 * @return [type] [description]
	 * @descripe 返回验证码的数据
	 */
	
	public function getVerify(){

		$this->addStr();//生成验证码
		
		$this->addPoint();//新增干扰点
		
		$this->addLine();//新增干扰线
		
		$time = time();
		
		$path = $this->path.'/'.$time.'.png';
		
		imagepng($this->img,$path);//输出图片
		
		$a = file_get_contents($path);
		
		imagedestroy($this->img);//销毁图片
		
		return array('verify'=>$this->verify,'img'=>PROJECT.'/'.$time.'.png');
	}


	/**
	 * [getStr description]
	 * @return [type] [description]
	 * @descripe 在图中写入验证码
	 */
	
	public function addStr(){

		$verify = '';//生成的验证码
		
		$verify_str = "asdfdfglfg74erf21854hgfhg556fhkg4l78jkghjz9xcvv123trtywiqpoqpwepdfgvnjytyut123133456456q";//字符串 从该字符取的字符 组成字符串
		
		for ($i=0; $i < 4 ; $i++) { 
		
			$text_size = mt_rand(5,10);//字体大小
		
			$text_color = imagecolorallocate($this->img,mt_rand(0,120),mt_rand(0,120),mt_rand(0,120));//字体的颜色
		
			$text_str = substr($verify_str,mt_rand(0,strlen($verify_str)),1);//随机取一个字符
		
			$verify .= $text_str;//生成的验证码
		
			$x = $i * 20 + mt_rand($this->config['x_size'] / 6,$this->config['x_size'] / 3 );//字体的坐标
		
			$y = mt_rand($this->config['y_size'] / 6,$this->config['y_size'] / 3 );//字体的坐标
		
			imagestring($this->img,$text_size,$x,$y,$text_str,$text_color);
		
		}
		
		$this->verify = $verify;
	}


	/**
	 * [addPoint description]
	 * @descripe 在图片增加干扰点
	 */
	
	public function addPoint(){

		for ($i=0; $i < 100; $i++) { 

			$point_color = 	imagecolorallocate($this->img,mt_rand(0,120),mt_rand(0,120),mt_rand(0,120));

			imagesetpixel($this->img ,mt_rand(0,$this->config['x_size']),mt_rand(0,$this->config['y_size']),$point_color);
		}
	}


	/**
	 * [addLine description]
	 * @descripe 给图片增加干扰线
	 */
	
	public function addLine(){

		for ($i=0; $i < 3; $i++) { 

			$line_color = imagecolorallocate($this->img,mt_rand(0,120),mt_rand(0,120),mt_rand(0,120));

			$x1 = $this->config['x_size'] -1;

			$y1 = $this->config['y_size'] -1 ;

			$x2 = $this->config['x_size'] -1;

			$y2 = $this->config['y_size'] -1;

			imageline($this->img,mt_rand(1,$x1),mt_rand(1,$y1),mt_rand(1,$x2),mt_rand(1,$y2),$line_color);

		}

	}

}