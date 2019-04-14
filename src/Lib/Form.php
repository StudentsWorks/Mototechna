<?php

namespace App\Lib;
define('DEBUG', true);

abstract class Form
{
    public $data = [];
    protected $successful = false;
    protected $_controller;


    /***************GENERAL***************/
    public function successfull()
    {
        return $this->successful;
    }

    public static function className():string
    {
        return (new \ReflectionClass(get_called_class()))->getShortName();
    }

    /**
     * @return string Slugname of the form (e.g. registration, contact)
     */
    public static function name()
    {
        $class = static::className();
        $class = str_replace(["Form", __NAMESPACE__, "\\"], "", $class);
        $class = strtolower($class);
        return $class;
    }

    public function selectFormData($body)
    {
        return $body[$this->className()];
    }


    /***************ABSTRACT***************/

    abstract public function labels():array;

    /**
     * @param $name (Fieldname)
     * @return string
     */
    public function getLabel($name)
    {
        return $this->labels()[$name];
    }


    abstract public function getData(): ?array;

    abstract public function setCSRF(string $token);

    abstract public function getCSRF();


    /***************SINGLETON***************/

    protected static $instance;

    public static function form()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;

    }

    /***************CORE***************/

    public function __construct()
    {
        if ($this->getData()) {
            $this->execute();
        }
    }

    public function execute()
    {
        $data = $this->selectFormData($this->getData());

        // 1. Check CSRF
        if (!$this->validateCSRF($data['csrf'] ?? '')) {
            die("Error: Security breach attempt");
        }

        // 2. Protect XSS (Sanitize(formData))
        $this->sanitize($data);

        // 3. Validate and act upon submission
        if ($this->validate()) {
            $this->process();
        }
    }

    /**
     * Processes REQUEST data into object property
     * @param $data
     */
    protected function sanitize($data)
    {
        // Basic version only saves data w/o any protection!
        $this->data = $data;
    }

    /**
     *  Processes form data
     */
    public function process()
    {
        if (defined('DEBUG')) {
            echo "<pre>";
            print_r($this->data);
            echo "</pre>";
        }
        $this->successful = true;
    }


    /***************CSRF***************/

    public function validateCSRF($token = null)
    {
        // if token stored in session is the same as token provided in request
        return true;
    }


    /***************ERROR***************/

    protected $_errors = [];

    public function addError($name, $message)
    {
        $this->_errors[$name] = $this->_errors[$name] ?? [];
        $this->_errors[$name][] = $message;
    }

    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    public function hasError($name)
    {
        return isset($this->_errors[$name]);
    }

    public function getErrors()
    {
        return $this->_errors;
    }


    /***************VALIDATION***************/

    public function rules():array
    {
        return [];
    }

    public function rulesForField($name):array
    {
        $new = [];
        foreach ($this->rules() as $rule) {
            $validator = $rule[0];
            $params = $rule[1];

            $field = array_shift($params);
            if ((is_array($field) && in_array($name, $field)) || $field == $name ){
                array_unshift($params, $name);
                $new[] = [$validator, $params];
            }
        }
        return $new;
    }

    public function validate($rules=null):bool
    {
        $result = true;
        $rules = $rules ?? $this->rules();

        foreach ($rules as $rule) {
            $validator = $rule[0];
            $params = $rule[1];

            $field = array_shift($params);

            if (is_array($field)) {

                foreach ($field as $name) {
                    $result &= $this->validateField($validator, $name, $params);
                }
            } else {
                $result &= $this->validateField($validator, $field, $params);
            }
        }
        return $result;
    }

    public function validateField($validator, $name, $args):bool
    {
        $func = 'validator' . ucfirst($validator);
        return static::$func($name, $args);
    }

    public function validatorEmpty($name, $args)
    {
        $v = !empty($this->data[$name]);

        if (!$v) {
            $this->addError($name, sprintf($args['msg'] ?? "Pole %s nesmie byť prázdne", $this->getLabel($name)));
        }
        return $v;
    }

    public function validatorMail($name, $args)
    {
        $v = filter_var($this->data[$name], FILTER_VALIDATE_EMAIL);
        if (!$v) {
            $this->addError($name, sprintf($args['msg'] ?? "%s musí byť platná emailová adresa.", $this->getLabel($name)));
        }
        return $v;
    }

    public function validatorCompare($name, $args)
    {
        switch ($args['operator'] ?? '=') {
            case '=':
                $v = $this->data[$name] == eval($args['to']);
                break;
            case '>':
                $v = $this->data[$name] > eval($args['to']);
                break;

        }
        if (!$v) {
            $this->addError($name, sprintf(($args['msg'] ?? "%s%s%s"), $this->getLabel($name), $args['operator']??'=', $args['to']));
        }
        return $v;
    }

    public function validatorExists($name, $args)
    {
        $v = isset($this->data[$name]);
        if (!$v) {
            $this->addError($name, sprintf(($args['msg'] ?? "%s not set"), $this->getLabel($name)));
        }
        return $v;
    }


    /***************HTML***************/

    /**
     * @param int $type (0 selfclosing, 1 open, -1 close
     * @param array $args
     * @return string
     */
    public function htmlTag($name, $type = 0, array $args = []): string
    {
        if ($type == -1) {
            return "</$name>";
        }
        $html = "<$name";
        foreach ($args as $arg => $value) {
            $html .= " $arg=\"$value\"";
        }
        $html .= $type == 0 ? "/>" : ">";
        return $html;
    }

    public function inputField($name, array $args = [])
    {
        $classes = explode(' ', $args['class'] ?? []);
        if ($this->hasError($name)) {
            $classes[] = 'is-invalid';
        }
        $args = array_merge($args, [
            'name' => $this->className() . "[$name]",
            'id' => $this->className() . "_$name",
            'value' => $this->data[$name] ?? '',
            'class' => implode(' ', $classes),
        ]);

        return $this->htmlTag('input', 0, $args);
    }

    public function textareaField($name, array $args = [])
    {
        $classes = explode(' ', $args['class'] ?? []);
        if ($this->hasError($name)) {
            $classes[] = 'is-invalid';
        }
        $args = array_merge($args, [
            'name' => $this->className() . "[$name]",
            'id' => $this->getFieldId($name),
            'class' => implode(' ', $classes),
        ]);

        return $this->htmlTag('textarea', 1, $args) . ($this->data[$name] ?? '') . $this->htmlTag('textarea', -1);
    }

    public function checkboxField($name, array $args = [])
    {
        $classes = explode(' ', $args['class'] ?? []);
        if ($this->hasError($name)) {
            $classes[] = 'is-invalid';
        }

        $args = array_merge($args, [
            'name' => $this->className() . "[$name]",
            'id' => $this->className() . "_$name",
            'type' => 'checkbox',
            'class' => implode(' ', $classes),

        ]);
        if (isset($this->data[$name])) {
            $args = array_merge($args, [
                'checked' => 'checked'
            ]);
        }
        return $this->htmlTag('input', 0, $args);
    }

    public function selectField($name, $options, array $args = [])
    {
        $classes = explode(' ', $args['class'] ?? []);
        if ($this->hasError($name)) {
            $classes[] = 'is-invalid';
        }
        $html = "";
        $args = array_merge($args, [
            'name' => $this->className() . "[$name]",
            'id' => $this->className() . "_$name",
            'class' => implode(' ', $classes),
        ]);

        $html.=$this->htmlTag('select', 1, $args);

        $html.=$this->htmlTag('option', 1,
                [
                    'disabled'=>'disabled',
                    'selected'=>isset($this->data[$name]) ? 'false' : 'selected',

                ]
            ).'Prosím vyberte'.$this->htmlTag('option', -1)."\n";
        foreach ($options as $key => $value) {
            $dd = isset($this->data[$name]) ? ($this->data[$name]==$key ? ['selected'=>'selected'] : [] ) : [];
            $html.=$this->htmlTag('option', 1,
                    array_merge([
                        'value'=>$key,

                    ], $dd
                    )).$value.$this->htmlTag('option', -1)."\n";
        }
        return $html.$this->htmlTag('select', -1);
    }

    /**
     * @param $name (Field name)
     * @param $label (Label text)
     * @param array $args
     * @return string Label tag
     */
    public function labelFor($name, $label = '', array $args = [], $required=false)
    {
        if (empty($label)) {
            $label = $this->getLabel($name);
        }

        $args = array_merge(['for' => $this->getFieldId($name)], $args);
        $tag = $this->htmlTag('label', 1, $args);
        $tag .= $label !== -1 ? $label . (!$required ? '' : '<span class="required">*</span>') . $this->htmlTag('label', -1) : '';
        return $tag;
    }


    public function getFieldId($name)
    {
        return $this->className() . '_' . $name;
    }

    public function hasErrorClass($name, $print = false)
    {
        $class = $this->hasError($name) ? ' is-invalid' : '';
        if ($print) {
            echo $class;
        } else {
            return $class;
        }
    }

    public function errorSummary()
    {
        $res = "<ul class=\"error-summary\">\n";
        foreach ($this->_errors as $field => $errors) {
            foreach ($errors as $msg) {
                $res .= "<li data-target=\"#" . $this->className() . '_' . $field . '">' . $msg . "</li>\n";
            }
        }
        return $res . '</ul>';
    }
}