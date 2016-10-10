<?php
class Translator
{
	private $current_index;
	private $inputCode;
	private $n;
	private $n_max;
	private $k;
	private $vars = array();//массив переменных
							  /*
							  array{
								['название переменной'] => значение;
							  }
							  */
	private $alf;//алфавит
	private $nums;//n-ричная система счисления
	private $debug;//0-не будет выводиться информация о вызовах функций; 1 - будет
	function __construct($text="",$debug=false)
	{
		$this->debug=$debug;
		if ($this->debug)
			echo "<br>__contruct()";
		$this->n=0;
		$this->n_max=2;
		$this->k=0;
		$this->current_index=0;
		$this->inputCode=$text;
	}
	//decoct переводит число из 10-ричной системы в 8-ричную
	private function getEight($x)
	{
		if($x<0)
		{
			$x = abs($x);
			return -decoct($x);
		}
		else
		{
			$x = abs($x);
			return decoct($x);
		}
	}	
	//octdec переводит число из 8-ричной системы в десятичную
	private function getDecimal($x)
	{
		if ($x<0)
		return -octdec($x);
		else
		return octdec($x);
	}
	//сложение в 8-ричной системе счисления
	private function add($x1,$x2)
	{
		$x1 = $this->getDecimal($x1);
		$x2 = $this->getDecimal($x2);
		$y = $x1+$x2;
		$y= $this->getEight($y);
		return $y;
	}
	//вычитание в 8-ричной системе счисления
	private function sub($x1,$x2)
	{
		$y=0;
		$x1 = $this->getDecimal($x1);
		$x2 = $this->getDecimal($x2);
		$y = $x1-$x2;
		$y= $this->getEight($y);
		return $y;
	}
	//умножение  в 8-ричной системе счисления
	private function mult($x1,$x2)
	{
		$y=0;
		$x1 = $this->getDecimal($x1);
		$x2 = $this->getDecimal($x2);
		$y = $x1*$x2;
		$y= $this->getEight($y);
		return $y;
	}
	//деление  в 8-ричной системе счисления
	private function div($x1,$x2)
	{
		$y=0;
		$x1 = $this->getDecimal($x1);
		$x2 = $this->getDecimal($x2);
		$y = round($x1/$x2);
		$y= $this->getEight($y);
		return $y;
	}
	//степенная операция в 8-ричной системе счисления
	private function power($x1,$x2)
	{
		$y=0;
		$x1 = $this->getDecimal($x1);
		$x2 = $this->getDecimal($x2);
		$y = pow($x1,$x2);
		$y= $this->getEight($y);
		return $y;
	}
	private function number($x)
	{
		if (mb_ereg("[0-7]", $x))
			return true;
		else
			return false;
	}
	private function letter($let)
	{
		if (mb_ereg("[а-яА-Я]", $let))
			return true;
		else
			return false;
	}
	public function printVars()
	{
		$html = "<ul>";
		foreach($this->vars as $key => $value)
		{
			$html.="<li>{$key} = {$value}</li>";
		}	
		$html .= "</ul>";
		return $html;
	}
	public function setText($text)
	{
		if ($this->debug)
			echo "<br>setText()";
		$this->inputCode = $text;
	}
	public function run()
	{
		if ($this->debug)
			echo "<br>run()";
		try{

			mb_internal_encoding("UTF-8");
			mb_regex_encoding("UTF-8");
			$interenc = mb_internal_encoding();
			$this->inputCode = mb_convert_encoding($this->inputCode,$interenc,"UTF-8");
			$this->inputCode = mb_ereg_replace("<span.*?>|<\/span>","",$this->inputCode);
			$this->inputCode = mb_ereg_replace("<p>","@",$this->inputCode);
			$this->inputCode = mb_ereg_replace("</p>","#",$this->inputCode);
			$this->inputCode = mb_ereg_replace("&nbsp;","",$this->inputCode);
			


			$this->language();
		}		
		catch(Exception $e) {
  		  echo '<div id="errors"><b>Ошибка:</b> ',  $e->getMessage(), "\n","</div>";
  		  $this->skipSpaces(true);
  		  if (mb_ereg_replace("\r|\n","",mb_substr($this->inputCode, $this->current_index,1))=="")
  		  {
  		  	$this->current_index+=2;
			$text= mb_ereg_replace("\r\n","",mb_substr($this->inputCode, 0,$this->current_index)).
  		  "<span style='background-color:red;'>"
  		  .mb_ereg_replace("\r\n","",mb_substr($this->inputCode, $this->current_index,1))
  		  ."</span>"
  		  .mb_ereg_replace("\r\n","",mb_substr($this->inputCode, $this->current_index+1,mb_strwidth($this->inputCode)-$this->current_index));
  		  }
  		  else
  		  {
  		  $text= mb_ereg_replace("\r\n","",mb_substr($this->inputCode, 0,$this->current_index)).
  		  "<span style='background-color:red;'>"
  		  .mb_ereg_replace("\r\n","",mb_substr($this->inputCode, $this->current_index,1))
  		  ."</span>"
  		  .mb_ereg_replace("\r\n","",mb_substr($this->inputCode, $this->current_index+1,mb_strwidth($this->inputCode)-$this->current_index));
  			}

  		  $text = mb_ereg_replace("@", "<p>", $text);
  		  $text = mb_ereg_replace("#", "</p>", $text);
  		  $text = mb_ereg_replace("\r\n", "", $text);
  		  //заменяем в нашем textarea тест на этот
  		  echo "<script>
  		  setText(\"{$text}\");
  		  </script>";
  		  exit();
  		  

		}
		$text = mb_ereg_replace("@", "<p>", $this->inputCode);
  		  $text = mb_ereg_replace("#", "</p>", $text);
  		  $text = mb_ereg_replace("\r\n", "", $text);
  		  echo "<script>
  		  setText(\"{$text}\");
  		  </script>";
	}
	//пропустить пробелы и перенос строки(вызывать до получения символа или проверки на терминал)
	private function skipSpaces($skipRN=false){
		while(true){
			$ch = $this->getChar($this->current_index);
			if ($ch=="&")
			{
			if (mb_substr($this->inputCode, $this->current_index,mb_strwidth("&nbsp;"))=="&nbsp;")
			{
				$this->current_index+= mb_strwidth("&nbsp;");
			}
			else
				break;

			}
			else
			if ($ch==" "|| $ch=="@"|| $ch=="#")
			{
			$this->current_index++;
				continue;
			}
			else if($skipRN)
			{
				if ($ch=="\r" || $ch=="\n")
				{

			$this->current_index++;
				continue;
				}else
				{
					break;
				
				}
			}else
			{
				break;
			
			}
			
		};
	}
	private function getChar($i)
	{
		return mb_substr($this->inputCode,$i,1);	
	}
	private function language()
	{
		if ($this->debug)
			echo "<br>language()";
		/*
		Программа
		zvenia();
		Конец
		*/
		$this->skipSpaces(true);
		if ( mb_substr($this->inputCode,$this->current_index,mb_strwidth("Программа")) != "Программа")
		{
			throw new Exception("Ожидалось слово \"Программа\"");
		}
		$this->current_index += mb_strwidth("Программа");;
		$this->skipSpaces();
		$this->zvenia();
		$this->skipSpaces();
		if (mb_substr($this->inputCode,$this->current_index,mb_strwidth("Конец")) != "Конец")
		{
			throw new Exception("Ожидалось слово \"Конец\"");
		}
		$this->current_index += mb_strwidth("Конец");
		$this->skipSpaces(true);
		if ($this->current_index<mb_strwidth($this->inputCode))
			throw new Exception("Лишний код после конца программы.");
	}
	private function zvenia()
	{
		if ($this->debug)
			echo "<br>zvenia()";
		/*
	for(;;) zveno()";"
		*/
		do{
		$this->skipSpaces();
			$this->zveno();
			if ($this->k!=0) return;
			if ($this->getChar($this->current_index)==";")//встретили ";"-> затем должно быть звено
			{
				$this->current_index++;
				continue;
			}
			$this->skipSpaces();
		}while( (mb_substr($this->inputCode,$this->current_index,mb_strwidth("Конец"))!="Конец"));//Пока не "Конец"

	}
	private function missInt($index)
	{
		if ($this->debug)
			echo "<br>missInt()";
		do{
			$ch = $this->getChar($index);
			$index++;
		}while(($this->number ($ch)));
			
			return $this->getChar($index);
	}
	private function zveno()
	{
		if ($this->debug)
			echo "<br>zveno()";
		/*
		Ввод for(;;) word()
		*/
		$this->skipSpaces(true);
		if (mb_substr($this->inputCode,$this->current_index,mb_strwidth("Ввод")) != "Ввод")
		{
			throw new Exception("Ожидалось слово \"Ввод\"");
		}
		$this->current_index += mb_strwidth("Ввод");
		do{
			$this->word(); 
			if ($this->current_index>mb_strwidth($this->inputCode))
				break;
			if ($this->k!=0) return;
			$this->skipSpaces(false);//тут надо как-то делить переменные по переводу строки
			if(($this->getChar($this->current_index)=="\r") and ($this->getChar($this->current_index+1)=="\n"))
			{
				$this->skipSpaces(true);
			}
			elseif(!(($this->missInt($this->current_index)!=":") and ($this->getChar($this->current_index)!=";") and  (mb_substr($this->inputCode,$this->current_index,mb_strwidth("Конец")) != "Конец")))
			{
				;//Выходим
			}
			
			else
			{
				throw new Exception("Ожидалось знак операции или перевод строки,или \";\"");

			}
		$this->skipSpaces();
		}while(($this->missInt($this->current_index)!=":") and ($this->getChar($this->current_index)!=";") and ($this->getChar($this->current_index)!=",") and (mb_substr($this->inputCode,$this->current_index,mb_strwidth("Конец")) != "Конец"));//второе слово : ---как?
		

	}
	public function word(){
		if ($this->debug)
			echo "<br>word()";
		/*
		int":"perem"="rightPart()
		*/
		$this->n=0;
		$this->skipSpaces(true);
			$this->int();
			//$this->current_index--;
		$this->skipSpaces(true);
			if ($this->getChar($this->current_index)!=":")
			{
				throw new Exception("Ожидалось \":\"");
			}
			$this->current_index++;
			$var_name = $this->perem();
		$this->skipSpaces();
			if ($this->getChar($this->current_index)!="=")
			{
				throw new Exception("Ожидалось \"=\"");
			}
			$this->current_index++;
			$val = $this->rightPart();
			$this->vars[$var_name] = $val;
			if ($this->debug)
				echo "<hr>perem = {$perem}<hr>";
			if ($this->k!=0) return;

	}
	//аддитивные
	public function rightPart()
	{
		if ($this->debug)
			echo "<br>rightPart()";
		$rp=0;
		/*
		for() block1() znak1()
		*/
		//тут что-то странное
		$this->skipSpaces();
		if ($this->getChar($this->current_index)=="-")//минус
		{
			$this->current_index++;
			$rp = -$this->block1();
			//echo $rp;
			if ($this->k!=0) return;
		}
		else
		{
			$rp = $this->block1();

			if ($this->k!=0) return;
		}
		do{
		$this->skipSpaces();
			//+
			if ($this->getChar($this->current_index)=="+")
			{
				$this->current_index++;
				$rp = $this->add($rp,$this->block1());
				if ($this->k!=0) return;
			}//-
			elseif($this->getChar($this->current_index)=="-")
			{
				$this->current_index++;
				$rp = $this->sub($rp,$this->block1());
				if ($this->k!=0) return;

			}
		}while(($this->getChar($this->current_index)=="+")||($this->getChar($this->current_index)=="-"));
		return $rp;

	}
	//мультипликативные
	private function block1()
	{
		
		/*
		for() block1() znak1()
		*/
		if ($this->debug)
			echo "<br>block1()";
		$rp=0;
	
			$block1 = $this->block2();
			if ($this->k!=0) return;
		while(($this->getChar($this->current_index)=="*")||($this->getChar($this->current_index)=="/")){
			$this->skipSpaces();
			//*
			if ($this->getChar($this->current_index)=="*")
			{
				$this->current_index++;
				//$rp*=$this->block3();
				$block1 = $this->mult($block1,$this->block2());
				if ($this->k!=0) return;
			}// / -деление
			elseif($this->getChar($this->current_index)=="/")
			{

				$this->current_index++;
				$block2 = $this->block2();
				if ($block2 == 0)
				{
					$this->current_index-=2;
					throw new Exception("Деление на ноль не разрешено.");
				}
				//$rp= $rp/$this->block3();
				$block1 = $this->div($block1,$block2);
				if ($this->k!=0) return;

			}
		}

		return $block1;
	}
	//сепенные
	private function block2()
	{
		/*
		for() block3() "^"
		*/
		if ($this->debug)
			echo "<br>block2()";
		$block2=0;
	
			$block2 =$this->block3();
			if ($this->k!=0) return;
		while(($this->getChar($this->current_index)=="^")){
			//+
			$this->skipSpaces();
			if ($this->getChar($this->current_index)=="^")
			{
				$this->current_index++;
				$block3 =$this->block3();
				$block2 = $this->power($block2,$block3);
			}
		}
		return $block2;
	}
	
	private function block3()
	{
		if ($this->debug)
			echo "<br>block3()";
		/*
		 int!perem!"("rightPart")"! "["rightPart"]" n<=2
		*/
		 $block3=0;
		$this->skipSpaces();
		 if($this->getChar($this->current_index)=="(")
		 {
		 	$this->current_index++;
			 	$block3=$this->rightPart();
			 	$this->skipSpaces();
		 	if($this->getChar($this->current_index)==")")
			 {
		 		$this->current_index++;
			 }
			 else
			 {
			 	if (!$this->number($this->getChar($this->current_index)))
			 	{
			 		throw new Exception("Ожидалось знак операции,(,переменная,целое или закрывающая круглая скобка");
			 	}
		 		throw new Exception("Нет закрывающей круглой скобки");
			 }
		}
		 elseif($this->getChar($this->current_index)=="[")
		 {
		 	$this->current_index++;
		 	$this->n++;
		 	if ($this->n>$this->n_max)
		 	{
		 		$k=1;
		 		throw new Exception("Вложенность квадратных скобок не должна превышать глубины 2");
		 	}

			 	$block3=$this->rightPart();
			$this->skipSpaces();
			 	if($this->getChar($this->current_index)=="]")
			 {
		 		$this->current_index++;
			 }
			 else
			 {
			 	if (!$this->number($this->getChar($this->current_index)))
			 	{
			 		throw new Exception("Ожидалось знак операции,(,переменная,целое или закрывающая квадратная скобка");
			 	}
		 		throw new Exception("Нет закрывающей квадратной скобки");
			 }

		 }
		 	elseif($this->letter($this->getChar($this->current_index)))//переменная
		 	{
		 		$block3=$this->getVar();
		 	}elseif($this->number($this->getChar($this->current_index)))//целое
		 	{
		 		$block3=$this->int();
		 	}
		 	else
		 	{
		 		throw new Exception("Ожидалось (,[,переменная или целое");
		 	}
		 	return $block3;
	}
	//получить значение переменно
	private function getVar()
	{
		if ($this->debug)
			echo "<br>getVar()";
		$name = $this->readVar();
		if (isset($this->vars[$name]))
			return $this->vars[$name];
		else
		{
			$this->current_index -= mb_strwidth($name);
			throw new Exception("Не известная переменная");
		}
	}
	//прочитать название переменной из строки
	private function readVar()
	{

		if ($this->debug)
			echo "<br>readVar()";
			$var="";
		$this->skipSpaces();
			if (!$this->letter($this->getChar($this->current_index)))
			{
				throw new Exception("Ожидалось буква");
			}
			$var.=$this->getChar($this->current_index);
			$this->current_index++;
			//3 цифры
			for ($i=0;$i<3;$i++)
			{
				$this->skipSpaces();
				$ch = $this->getChar($this->current_index);
				$var.=$this->getChar($this->current_index);
				$this->current_index++;
				if (!($this->number ($ch)))
				{
					throw new Exception("Ожидалась цифра");
				}

			}
		return $var;
	}
	private function perem()
	{
		if ($this->debug)
			echo "<br>perem()";
			//считываем переменную
			//буква
			$name=$this->readVar();
			return $name;
	}
	private function int()
	{
		$x="";
		if ($this->debug)
			echo "<br>int()";
		$this->skipSpaces();
		if ($this->number($this->getChar($this->current_index)))
			{
		do{

		$this->skipSpaces();
			if ($this->number($this->getChar($this->current_index)))
			{
				$x.=$this->getChar($this->current_index);
				$this->current_index++;
			}
			else
			{
				break;
			}

		}while(true);
	}	
	else
	{
		throw new Exception("Ожидалось целое");
	}
		return (int)$x;
	
	}

}
?>