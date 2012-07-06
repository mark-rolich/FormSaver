<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN"
        "http://www.w3.org/TR/1998/REC-html40-19980424/strict.dtd">
<html>
<head>
<title>FormSaver example</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
</head>
<body>
<?php
ob_start();
include 'FormSaver.php';

$excludeFields = array('pwd');
$form = new FormSaver($excludeFields);
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
    <ul>
        <li>
            <label>Textfield</label>
            <input type="text" name="firstname">
        </li>
        <li>
            <label>Textarea</label>
            <textarea name="textarea">&nbsp;</textarea>
        </li>                
        <li>
            <label>Textfield as array</label>
            <input type="text" name="test[test][test]">
        </li>
        <li>
            <label>Excluded textfield (e.g. password)</label>
            <input type="password" name="pwd">
        </li>
        <li>
            <label>Select box</label>
            <select name="select">
                <option value="0">Option 1</option>
                <option value="1">Option 2</option>
                <option value="2">Option 3</option>
            </select>
        </li>
        <li>
            <label>Select box multiple</label>
            <select name="select_multiple" multiple size="5">
                <option value="0">Option 1</option>
                <option value="1">Option 2</option>
                <option value="2">Option 3</option>
                <option value="3">Option 4</option>
                <option value="4">Option 5</option>
            </select>
        </li>
        <li>
            <label>Single checkbox</label>
            <label for="single-check">Option 1<input type="checkbox" name="single_check" id="single-check"></label>
        </li>        
        <li>
            <label>Checkbox group</label>
            <label for="check-option-1">Option 1<input type="checkbox" name="check[test][]" value="0" id="check-option-1"></label>
            <label for="check-option-2">Option 2<input type="checkbox" name="check[test][]" value="1" id="check-option-2"></label>
            <label for="check-option-3">Option 3<input type="checkbox" name="check[test][]" value="2" id="check-option-3"></label>
        </li>
        <li>
            <label>Radio group</label>
            <label for="radio-option-1">Option 1<input type="radio" name="radio" value="0" id="radio-option-1"></label>
            <label for="radio-option-2">Option 2<input type="radio" name="radio" value="1" id="radio-option-2"></label>
            <label for="radio-option-3">Option 3<input type="radio" name="radio" value="2" id="radio-option-3"></label>
        </li>
        <li>
            <input type="submit" value="Send" name="asdf">
        </li>
    </ul>
</form>
<?php
$form->save();
?>
</body>
</html>