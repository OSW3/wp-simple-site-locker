<?php

if (!class_exists('OSW3_FormType'))
{
    abstract class OSW3_FormType
    {
        private $errors = [];
        private $config = null;
        private $attributes = null;
        private $type = null;
        private $name = null;
        private $id = null;
        private $value = null;
        private $placeholder = null;
        private $class = null;
        private $cols = null;
        private $rows = null;
        private $checked = null;
        private $required = null;
        private $disabled = null;
        private $readonly = null;
        private $multiple = null;
        private $choices = null;
        private $step = null;
        
        private $hasLabelTag = false;
        private $hasWrapper = false;
        private $label = null;
        private $helper = null;

        public function __construct( $params )
        {
            $this->config       = isset($params['config']) ? $params['config'] : false;
            $this->attributes   = isset($params['attributes']) ? $params['attributes'] : false;
            $this->hasLabelTag  = isset($params['addLabelTag']) ? $params['addLabelTag'] : false;
            $this->hasWrapper   = isset($params['addWrapper']) ? $params['addWrapper'] : false;
            $this->errors       = isset($params['errors']) ? $params['errors'] : [];
            $attrNameAsArray    = isset($params['attrNameAsArray']) ? $params['attrNameAsArray'] : false;
            
            $this->setKey();
            $this->setType();
            $this->setName($attrNameAsArray);
            $this->setId();
            $this->setValue();
            $this->setPlaceholder();
            $this->setClass();
            $this->setCols();
            $this->setRows();
            $this->setChecked();
            $this->setRequired();
            $this->setDisabled();
            $this->setReadonly();
            $this->setMultiple();
            $this->setTag();
            $this->setChoices();
            $this->setStep();

            $this->setLabel();
            $this->setHelper();
        }

        // protected function renderField($type, $showLabel, $wrapper, $attrs, $before, $after, $x=null)
        protected function renderField( $attributes )
        {
            $output = null;
            $attributes = trim(implode(" ", $attributes));

            // Form control
            switch ( strtolower( $this->getTag() ) )
            {
                default:
                    $control = $this->getHtmlTag_input( $attributes );
                    break;
                    
                case 'choices':
                    $control = $this->getHtmlTag_select( $attributes );
                    break;
                    
                case 'choices_as_radio':
                case 'choices_as_checkbox':
                    $control = $this->getHtmlChoices();
                    break;
                
                case 'textarea':
                    $control = $this->getHtmlTag_textarea( $attributes );
                    break;
            }

            // The label
            if ($this->hasLabelTag) $output.= $this->getHtmlTag_label();

            $output.= $control;

            // The Error
            $output.= $this->getHtmlTag_error();

            // The Helper
            $output.= $this->getHtmlTag_helper();

            // The wrapper
            if ($this->hasWrapper) $output = $this->getHtmlTag_wrapper( $output );

            return $output;
        }


        /**
         * HTML Tags
         */
        private function getHtmlTag_input( $attributes )
        {
            return "<input $attributes />";
        }
        private function getHtmlTag_select( $attributes )
        {
            return "<select $attributes >". $this->getHtmlChoices() ."</select>";
        }
        private function getHtmlTag_textarea( $attributes )
        {
            return "<textarea $attributes >". $this->getValue() ."</textarea>";
        }
        private function getHtmlTag_wrapper( $control )
        {
            return "<div class=\"osw3-plugin\">". $control ."</div>";
        }
        private function getHtmlTag_helper()
        {
            return "<p class=\"description helper\">". $this->getHelper() ."</p>";
        }
        private function getHtmlTag_error()
        {
            if (isset($this->errors[ $this->getKey() ]))
            {
                return "<div class=\"has-error\">". $this->errors[ $this->getKey() ] ."</div>";
            }
            return null;
        }
        private function getHtmlTag_label()
        {
            return "<label for=\"". $this->getId() ."\">". $this->getLabel() ."</label>";
        }
        

        /**
         * Set Tag
         */
        private function setTag()
        {
            switch ($this->getType())
            {
                case 'choices':
                    $this->tag = "choices";

                    if ( isset($this->attributes->expanded) && true === $this->attributes->expanded )
                    {
                        if ($this->getMultiple())
                        {
                            $this->tag = "choices_as_checkbox";
                        }
                        else
                        {
                            $this->tag = "choices_as_radio";
                        }
                    }
                    break;
                
                case 'textarea':
                    $this->tag = "textarea";
                    break;
                
                default:
                    $this->tag = "input";
                    break;
            }

            
        }
        /**
         * Get Tag Value
         */
        private function getTag()
        {
            return $this->tag;
        }
        

        /**
         * Set Key
         */
        private function setKey()
        {
            $this->key = $this->attributes->key;
        }
        /**
         * Get Key
         */
        private function getKey()
        {
            return $this->key;
        }


        /**
         * Set Type
         */
        private function setType()
        {
            $value = isset($this->attributes->type) 
                ? $this->attributes->type 
                : "text";
            
            $attribute = 'type="'.$value.'"';
            
            $this->type = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Type Value
         */
        protected function getType()
        {
            return $this->type->value;
        }
        /**
         * Get Type Attribute
         */
        protected function getAttrType()
        {
            return $this->type->attribute;
        }
        

        /**
         * Set Name
         */
        private function setName( $attrNameAsArray = false )
        {
            $value = isset($this->attributes->key) 
                ? $this->attributes->key 
                : null;
                
            $attribute = null;

            if (null != $value)
            {
                $attribute = $attrNameAsArray === true
                    ? $this->config->Namespace.'['.$value.']'
                    : $value;
                
                $attribute = 'name="'.$attribute.'"';
            }
            
            $this->name = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Name Value
         */
        protected function getName()
        {
            return $this->name->value;
        }
        /**
         * Get Name Attribute
         */
        protected function getAttrName()
        {
            return $this->name->attribute;
        }
        

        /**
         * Set Id
         */
        private function setId()
        {
            $value = isset($this->attributes->key) 
                ? $this->config->Prefix.$this->attributes->key 
                : null;
                
            $attribute = null != $value 
                ? 'id="'.$value.'"'
                : null;
            
            $this->id = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Id Value
         */
        protected function getId()
        {
            return $this->id->value;
        }
        /**
         * Get Id Attribute
         */
        protected function getAttrId()
        {
            return $this->id->attribute;
        }
        

        /**
         * Set Value
         */
        private function setValue()
        {
            $value = isset($this->attributes->value) 
                ? addslashes(__($this->attributes->value, $this->config->Namespace ))
                : null;
            
            if (null == $value)
            {
                $value = isset($this->attributes->default) 
                    ? $this->attributes->default 
                    : null;
            }

            $attribute = null;

            if (null != $value)
            {
                switch ( $this->getType() )
                {
                    case 'checkbox':
                        $attribute = 'value="on"';
                        break;
                    
                    default:
                        $attribute = 'value="'.$value.'"';
                        break;
                }
            }

            $this->value = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Value Value
         */
        protected function getValue()
        {
            return $this->value->value;
        }
        /**
         * Get Value Attribute
         */
        protected function getAttrValue()
        {
            return $this->value->attribute;
        }
        

        /**
         * Set Placeholder
         */
        private function setPlaceholder()
        {
            $value = isset($this->attributes->placeholder) 
                ? addslashes(__($this->attributes->placeholder, $this->config->Namespace ))
                : null;

            $attribute = null != $value 
                ? 'placeholder="'.$value.'"'
                : null;

            $this->placeholder = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Placeholder Value
         */
        protected function getPlaceholder()
        {
            return $this->placeholder->value;
        }
        /**
         * Get Placeholder Attribute
         */
        protected function getAttrPlaceholder()
        {
            return $this->placeholder->attribute;
        }
        

        /**
         * Set Class
         */
        private function setClass()
        {
            $value = isset($this->attributes->class) 
                ? $this->attributes->class 
                : null;

            $attribute = null != $value 
                ? 'class="'.$value.'"'
                : null;

            $this->class = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Class Value
         */
        protected function getClass()
        {
            return $this->class->value;
        }
        /**
         * Get Class Attribute
         */
        protected function getAttrClass()
        {
            return $this->class->attribute;
        }
        

        /**
         * Set Cols
         */
        private function setCols()
        {
            $value = null;

            if ( "textarea" === $this->getType() )
            {
                $value = isset($this->attributes->cols) 
                    ? intval($this->attributes->cols)
                    : null;
            }

            $attribute = null != $value 
                ? 'cols="'.$value.'"'
                : null;

            $this->cols = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Cols Value
         */
        protected function getCols()
        {
            return $this->cols->value;
        }
        /**
         * Get Cols Attribute
         */
        protected function getAttrCols()
        {
            return $this->cols->attribute;
        }
        

        /**
         * Set Rows
         */
        private function setRows()
        {
            $value = null;

            if ( "textarea" === $this->getType() )
            {
                $value = isset($this->attributes->rows) 
                    ? intval($this->attributes->rows)
                    : null;
            }

            $attribute = null != $value 
                ? 'rows="'.$value.'"'
                : null;

            $this->rows = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Rows Value
         */
        protected function getRows()
        {
            return $this->rows->value;
        }
        /**
         * Get Rows Attribute
         */
        protected function getAttrRows()
        {
            return $this->rows->attribute;
        }
        

        /**
         * Set Checked
         */
        private function setChecked()
        {
            $value = false;

            if ( "checkbox" === $this->getType() )
            {
                $value = "on" === $this->getValue();
            }

            $attribute = false !== $value 
                ? 'checked'
                : null;

            $this->checked = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Checked Value
         */
        protected function getChecked()
        {
            return $this->checked->value;
        }
        /**
         * Get Checked Attribute
         */
        protected function getAttrChecked()
        {
            return $this->checked->attribute;
        }
        

        /**
         * Set Required
         */
        private function setRequired()
        {
            $value = isset($this->attributes->required) 
                ? $this->attributes->required
                : false;

            $attribute = true === $value 
                ? 'required="required"'
                : null;

            $this->required = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Required Value
         */
        protected function getRequired()
        {
            return $this->required->value;
        }
        /**
         * Get Required Attribute
         */
        protected function getAttrRequired()
        {
            return $this->required->attribute;
        }
        

        /**
         * Set Disabled
         */
        private function setDisabled()
        {
            $value = isset($this->attributes->disabled) 
                ? $this->attributes->disabled
                : false;

            $attribute = true === $value 
                ? 'disabled="disabled"'
                : null;

            $this->disabled = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Disabled Value
         */
        protected function getDisabled()
        {
            return $this->disabled->value;
        }
        /**
         * Get Disabled Attribute
         */
        protected function getAttrDisabled()
        {
            return $this->disabled->attribute;
        }
        

        /**
         * Set Readonly
         */
        private function setReadonly()
        {
            $value = isset($this->attributes->readonly) 
                ? $this->attributes->readonly
                : false;

            $attribute = true === $value 
                ? 'readonly="readonly"'
                : null;

            $this->readonly = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Readonly Value
         */
        protected function getReadonly()
        {
            return $this->readonly->value;
        }
        /**
         * Get Readonly Attribute
         */
        protected function getAttrReadonly()
        {
            return $this->readonly->attribute;
        }
        

        /**
         * Set Multiple
         */
        private function setMultiple()
        {
            $value = false;

            if ( "choices" === $this->getType() )
            {
                $value = isset($this->attributes->multiple) 
                    ? $this->attributes->multiple
                    : false;
            }

            $attribute = true === $value 
                ? 'multiple="multiple"'
                : null;

            $this->multiple = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Multiple Value
         */
        protected function getMultiple()
        {
            return $this->multiple->value;
        }
        /**
         * Get Multiple Attribute
         */
        protected function getAttrMultiple()
        {
            return $this->multiple->attribute;
        }
        

        /**
         * Set Choices
         */
        private function setChoices()
        {
            $value = null;
            $html = null;

            if ( "choices" === $this->getType() )
            {
                if (isset($this->attributes->choices))
                {
                    if (is_string($this->attributes->choices))
                    {
                        $value = OSW3::tryToDo( $this->attributes->choices );
                    }
                    else
                    {
                        $value = $this->attributes->choices;
                    }
                }
            }

            if (is_array($value))
            {
                $selected_value = $this->getValue();

                foreach ($value as $key => $choice)
                {
                    $is_selected = $key == $selected_value;

                    $value[$key] = [
                        "label" => $choice,
                        "value" => $key,
                        "is_selected" => $is_selected,
                    ];

                    switch ($this->getTag())
                    {
                        case 'choices_as_radio':
                            $html.= !empty($html) ? "<br>\n" : null;

                            $this->type = (object) [
                                "value" => "radio",
                                "attribute" => 'type="radio"'
                            ];
                            
                            $this->value = (object) [
                                "value" => $value[$key]['value'],
                                "attribute" => 'value="'.$value[$key]['value'].'"'
                            ];
                            
                            $this->checked = (object) [
                                "value" => $value[$key]['is_selected'] ? true : false,
                                "attribute" => $value[$key]['is_selected'] ? "checked" : null
                            ];
                            
                            $html.= "<label>";
                            $html.= $this->getHtmlTag_input(implode(" ",[
                                $this->getAttrType(),
                                $this->getAttrName(),
                                $this->getAttrId(),
                                $this->getAttrClass(),
                                $this->getAttrValue(),
                                $this->getAttrRequired(),
                                $this->getAttrDisabled(),
                                $this->getAttrReadonly(),
                                $this->getAttrChecked(),
                            ]));
                            $html.= $value[$key]['label'];
                            $html.= "</label>\n";
                            break;

                        case 'choices_as_checkbox':
                            $html.= !empty($html) ? "<br>\n" : null;

                            $this->type = (object) [
                                "value" => "checkbox",
                                "attribute" => 'type="checkbox"'
                            ];
                            
                            $this->value = (object) [
                                "value" => $value[$key]['value'],
                                "attribute" => 'value="'.$value[$key]['value'].'"'
                            ];
                            
                            $this->name = (object) [
                                "value" => $this->getName(),
                                "attribute" => 'name="'.$this->config->Namespace.'['.$this->getName().']['.$value[$key]['value'].']"'
                            ];
                            
                            $this->checked = (object) [
                                "value" => $value[$key]['is_selected'] ? true : false,
                                "attribute" => $value[$key]['is_selected'] ? "checked" : null
                            ];
                            
                            $html.= "<label>";
                            $html.= $this->getHtmlTag_input(implode(" ",[
                                $this->getAttrType(),
                                $this->getAttrName(),
                                $this->getAttrId(),
                                $this->getAttrClass(),
                                $this->getAttrValue(),
                                $this->getAttrRequired(),
                                $this->getAttrDisabled(),
                                $this->getAttrReadonly(),
                                $this->getAttrChecked(),
                            ]));
                            $html.= $value[$key]['label'];
                            $html.= "</label>\n";
                            break;

                        default:
                            $selected = $value[$key]['is_selected'] ? " selected" : null;
                            $html.= "<option value=\"".$value[$key]['value']."\"".$selected.">";
                            $html.= $value[$key]['label'];
                            $html.= "</option>";
                            break;
                    }
                }
            }

            $this->choices = (object) [
                "value" => $value,
                "html" => $html,
            ];
        }
        /**
         * Get Choices Value
         */
        private function getChoices()
        {
            return $this->choices->value;
        }
        /**
         * Get Choices HTML
         */
        private function getHtmlChoices()
        {
            return $this->choices->html;
        }
        

        /**
         * Set Step
         */
        private function setStep()
        {
            $value = isset($this->attributes->step) 
                ? $this->attributes->step 
                : null;
                
            $attribute = null != $value 
                ? 'step="'.$value.'"'
                : null;
            
            $this->step = (object) [
                "value" => $value,
                "attribute" => $attribute,
            ];
        }
        /**
         * Get Step Value
         */
        protected function getStep()
        {
            return $this->step->value;
        }
        /**
         * Get Step Attribute
         */
        protected function getAttrStep()
        {
            return $this->step->attribute;
        }
        

        /**
         * Set Label
         */
        private function setLabel ()
        {
            $this->label = isset($this->attributes->label) 
                ? addslashes(__($this->attributes->label, $this->config->Namespace ))
                : null;
        }
        /**
         * Get Label
         */
        private function getLabel ()
        {
            return $this->label;
        }
        

        /**
         * Set Helper
         */
        private function setHelper ()
        {
            $this->helper = isset($this->attributes->helper) 
                ? addslashes(__($this->attributes->helper, $this->config->Namespace ))
                : null;
        }
        /**
         * Get Helper
         */
        private function getHelper ()
        {
            return $this->helper;
        }
    }
}
