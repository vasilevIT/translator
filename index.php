<head>
<link href="style/style.css" rel="stylesheet" type="text/css"></style>
<title>Транслятор</title>
<script src="scripts/tinymce/tinymce.min.js"></script>
  <script>tinymce.init({selector:"textarea"});</script>
<script src="scripts/jquery.js"></script>
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
$bnf = "
<div id='bnf'>
<h3>БНФ</h3>
<p><b>Язык</b> = <span class='terminal'>\"Программа\"</span> Звенья <span class='terminal'>\"Конец\"</span></p>
<p><b>Звенья</b> = Звено <span class='terminal'>\";\"</span>...Звено</p>
<p><b>Звено</b> = <span class='terminal'>\"Ввод\"</span> Слово...Слово</p>
<p><b>Слово</b> = Метка<span class='terminal'>\":\"Перем <span class='terminal'>\"=\"</span> Прав.часть</p>
<p><b>Прав.часть</b> = &lt/<span class='terminal'>\"-\"</span>/&gtБлок1 Знак1 ... Блок1</p>
<p><b>Зк1</b> = <span class='terminal'>\"+\"</span>!<span class='terminal'>\"-\"</span></p>
<p><b>Блок1</b> = Блок2 Знак2 ... Блок2</p>
<p><b>Знак2</b> = <span class='terminal'>\"*\"</span>!<span class='terminal'>\"/\"</span></p>
<p><b>Блок2</b> = Блок3 <span class='terminal'>\"^\"</span> ... Блок3</p>
<p><b>Блок3</b> = Перем ! Цел ! <span class='terminal'>\"(\"</span> Прав.часть<span class='terminal'>\")\"</span> ! <span class='terminal'>\"[\"</span> Прав.часть<span class='terminal'>\"]\"</span>  N_квадр = 2</p>
<p></p>
</div>
";
if (isset($_POST['code']))
{
	$code = $_POST['code'];
}
else
{
$code="

<p>Программа</p>
<p>Ввод</p>
<p>44:м123=4+5</p>
<p>6:п666=5-2+77</p>
<p>;</p>
<p>Ввод</p>
<p>24:п311=4+5+м123</p>
<p>56:п444=3-4-2+111</p>
<p>Конец</p>
";
}
$form="
<div id='form'>
<form method='post'>
<textarea id='code' cols='30' rows='20' name='code'>{$code}</textarea><br>
<input type='submit'>
</form>
</div>
{$bnf}
";
echo $form;
$translator = new Translator();
$translator->setText($code);
$translator->run(); // инициализация
echo "<div id='vars'>Переменные:".$translator->printVars()."</div>";
?>