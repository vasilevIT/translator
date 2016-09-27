<head>
<title>Транслятор</title>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script>tinymce.init({selector:"textarea"});</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
function setText(text)
{

	jQuery("#code").html(text);
}
</script>
</head>
<?php
//github hello
require_once(__DIR__."/classes/translator.class.php");
if (isset($_POST['code']))
{
	$code = $_POST['code'];
}
else
{
$code="
<p>Программа</p>
<p>Ввод</p>
<p>44:м123=4+5,</p>
<p>6:п666=5-2+77</p>
<p>;</p>
<p>Ввод</p>
<p>24:п311=4+5+105,</p>
<p>56:п444=3-4-2+111</p>
<p>Конец</p>
";
}
$form="
<form method='post'>
<textarea id='code' cols='30' rows='20' name='code'>{$code}</textarea><br>
<input type='submit'>
</form>
";
echo $form;
$translator = new Translator();
$translator->setText($code);
$translator->run(); // инициализация
echo "Переменные:".$translator->printVars();
?>