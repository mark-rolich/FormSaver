<?php
/**
* Form values saver class
*
* Displays form field values submitted by POST
* without additional markup in the HTML form
*
* @author Mark Rolich <mark.rolich@gmail.com>
*/
class FormSaver
{
    /**
    * @var mixed - list of HTML form main elements
    */
    private $formElements = array('input', 'select', 'textarea');

    /**
    * @var mixed - list of fields which should not be displayed
    */
    private $excludeFields;

    /**
    * Constructor
    *
    * @param $excludeFields mixed - default empty array
    */
    public function __construct($excludeFields = array())
    {
        $this->excludeFields = $excludeFields;
    }

    /**
    * Get raw POST data and
    * convert it to associative array
    * with form field name strings as keys
    * and submitted POST values as values
    *
    * @return mixed
    */
    private function getRawPost()
    {
        $post = array();

        $rawpost = urldecode(file_get_contents('php://input'));

        if ($rawpost != '') {
            $chunks = explode('&', $rawpost);

            foreach ($chunks as $chunk) {
                $key = substr($chunk, 0, strpos($chunk, '='));

                if (!in_array($key, $this->excludeFields)) {
                    $value = (string)substr($chunk, strpos($chunk, '=') + 1);

                    if (array_key_exists($key, $post)) {
                        if (is_array($post[$key])) {
                            $post[$key] = array_values($post[$key]);
                        } else {
                            $post[$key] = array($post[$key]);
                        }

                        array_push($post[$key], $value);
                    } else {
                        $post[$key] = $value;
                    }
                }
            }
        }

        return $post;
    }

    /**
    * Handle SELECT form field,
    * set 'selected' attribute on posted option(s)
    *
    * @param $select mixed - DomElement object of SELECT form field
    * @param $value mixed - submitted value (or array of - if multiple attribute is set)
    */
    private function handleSelect($select, $value)
    {
        if ($select->hasAttribute('multiple') && is_array($value)) {
            foreach ($select->childNodes as $option) {
                if (in_array($option->getAttribute('value'), $value)) {
                    $option->setAttribute('selected', 'true');
                }
            }
        } else {
            foreach ($select->childNodes as $option) {
                if ($option->getAttribute('value') == $value) {
                    $option->setAttribute('selected', 'true');
                }
            }
        }
    }

    /**
    * Handle INPUT type=checkbox form field,
    * set 'checked' attribute on posted input(s)
    *
    * @param $checkbox mixed - DomElement object of INPUT type=checkbox form field
    * @param $value mixed - submitted value (or array of - if checkbox group is submitted)
    */
    private function handleCheckbox($checkbox, $value)
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                if ($checkbox->getAttribute('value') == $val) {
                    $checkbox->setAttribute('checked', 'true');
                }
            }
        } elseif ($checkbox->hasAttribute('value')
                  && $checkbox->getAttribute('value') == $value
                  || !$checkbox->hasAttribute('value')) {
            $checkbox->setAttribute('checked', 'true');
        }
    }

    /**
    * Parse form and get form fields,
    * display posted value depending on field type,
    * output resulting form to the browser
    */
    public function save()
    {
        $form = ob_get_clean();

        $dom = new DomDocument();
        $dom->loadHTML($form);

        foreach ($this->formElements as $element) {
            $list = $dom->getElementsByTagName($element);

            foreach ($list as $item) {
                $elements[] = $item;
            }
        }

        $post = $this->getRawPost();

        $checkPost = $elements[0]->getAttribute('name');

        if (isset($post[$checkPost])) {

            foreach ($elements as $element) {
                if ($element->hasAttribute('name')) {
                    $name = $element->getAttribute('name');

                    if (isset($post[$name])) {
                        if ($element->nodeName == 'select') {
                            $this->handleSelect($element, $post[$name]);
                        } elseif ($element->nodeName == 'input') {
                            switch ($element->getAttribute('type')) {
                                case 'checkbox':
                                    $this->handleCheckbox($element, $post[$name]);
                                    break;
                                case 'radio':
                                    if ($element->getAttribute('value') == $post[$name]) {
                                        $element->setAttribute('checked', 'true');
                                    }

                                    break;
                                default:
                                    $element->setAttribute('value', $post[$name]);
                            }
                        } else {
                            $element->nodeValue = $post[$name];
                        }
                    }
                }
            }

            $formNode = $dom->getElementsByTagName('form')->item(0);
            $form = html_entity_decode($dom->saveXML($formNode));
        }

        echo $form;

        ob_flush();
        flush();
    }
}
?>