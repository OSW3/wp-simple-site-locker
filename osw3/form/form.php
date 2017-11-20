<?php

if (!class_exists('OSW3_V1_FormType'))
{
    abstract class OSW3_V1_FormType
    {
        protected $state = null;
        protected $meta = null;

        private $label = null;
        private $options = null;
        private $attrName = null;
        private $attrPlaceholder = null;
        private $attrId = null;
        private $attrIdValue = null;
        private $attrClass = null;
        private $attrValue = null;
        private $attrCols = null;
        private $attrRows = null;
        private $attrMultiple = null;
        private $attrRequired = null;
        private $attrChecked = null;
        private $attrDisabled = null;
        private $attrReadonly = null;
        private $attrStep = null;

        public function __construct( $meta, $state=null )
        {
            $this->meta = $meta;
            $this->state = $state;

            $this->setLabel();
            $this->setAttrType();
            $this->setAttrName();
            $this->setAttrId();
            $this->setAttrClass();
            $this->setAttrValue();
            $this->setAttrPlaceholder();
            $this->setOptions();
            $this->setAttrCols();
            $this->setAttrRows();
            $this->setAttrMultiple();
            $this->setAttrRequired();
            $this->setAttrChecked();
            $this->setAttrDisabled();
            $this->setAttrReadonly();
            $this->setAttrStep();
        }

        protected function renderField($type, $showLabel, $wrapper, $attrs, $before, $after, $x=null)
        {
            $type   = strtolower($type);
            $attrs  = implode(" ", $attrs);
            $output = null;

            $label  = null;
            if ($showLabel) 
                $label  = "<label for=\"".$this->getId()."\">".$this->getLabel()."</label>";
            
            switch ($type)
            {
                default:
                    $control = $this->getHtmlInput($attrs);
                    break;
                    
                case 'checkbox':
                    $control = $this->getHtmlInput($attrs);
                    break;
                    
                case 'select':
                    $control = $this->getHtmlSelect($attrs);
                    break;
                
                case 'textarea':
                    $control = $this->getHtmlTextarea($attrs, $x);
                    break;
            }
            
            $output .= $wrapper ? '<div class="osw3-plugin">' : null;
            $output .= $label;
            $output .= $before;
            $output .= $control;
            $output .= $after;
            $output .= $wrapper ? '</div>' : null;

            return $output;
        }
        public function getHtmlInput($attrs)
        {
            return "<input $attrs />";
        }
        public function getHtmlSelect($attrs)
        {
            return "<select $attrs>".$this->getOptions()."</select>";
        }
        public function getHtmlTextarea($attrs, $value)
        {
            return "<textarea $attrs>$value</textarea>";
        }
        

        public function setAttrType()
        {
            $this->type = isset($this->meta->type) ? $this->meta->type : null;
        }
        protected function getAttrType ()
        {
            return 'type="'.$this->type.'"';
        }
        

        protected function setAttrName ()
        {
            $this->attrName = isset($this->meta->key) ? $this->meta->key : null;
        }
        protected function getAttrName ( $namespace )
        {
            if (!empty($this->attrName)) 
            {
                return 'name="'.$namespace.'['.$this->attrName.']"';
            }

            return null;
        }
        
        
        protected function setAttrId ()
        {
            $this->attrId = isset($this->meta->key) ? $this->meta->key : null;
        }
        protected function getAttrId ( $namespace = null )
        {
            if (!empty($this->attrId)) 
            {
                return 'id="'.$this->attrId.'"';
            }

            return null;
        }
        protected function getId()
        {
            return $this->attrId;
        }
        
        
        protected function setAttrClass ()
        {
            $this->attrClass = isset($this->meta->class) ? $this->meta->class : null;
        }
        protected function getAttrClass ( $namespace = null )
        {
            if (!empty($this->attrClass)) 
            {
                return 'class="'.$this->attrClass.'"';
            }

            return null;
        }
        
        
        protected function setAttrValue ()
        {
            $value = get_post_meta(
                get_post()->ID, 
                $this->meta->key, 
                true
            );

            if (empty($value) && isset($this->meta->value)) 
            {
                $value = $this->meta->value;
            }

            $default = isset($this->meta->default) 
                ? $this->meta->default 
                : null;
            
            if ( (is_bool($value) && false === $value) )
            {
                $value = false;
            }


            $this->attrValue = !empty($value) ? $value : $default;
            // if file type -> value is an array
            if (is_string($this->attrValue)) {
                $this->attrValue = addslashes($this->attrValue);
            }
        }
        protected function getAttrValue ()
        {
            if (!empty($this->attrValue)) 
            {
                return 'value="'.$this->attrValue.'"';
            }

            return null;
        }
        protected function getValue ()
        {
            return $this->attrValue;
        }
        
        
        protected function setAttrPlaceholder ()
        {
            $this->attrPlaceholder = isset($this->meta->placeholder) ? $this->meta->placeholder : null;
        }
        protected function getAttrPlaceholder ()
        {
            if (!empty($this->attrPlaceholder)) 
            {
                return 'placeholder="'.$this->attrPlaceholder.'"';
            }

            return null;
        }
        
        
        protected function setLabel ()
        {
            $this->label = isset($this->meta->label) ? $this->meta->label : null;
        }
        protected function getLabel ()
        {
            return $this->label;
        }
        
        
        protected function setOptions ()
        {
            if (isset($this->meta->options))
            {
                if (is_string($this->meta->options))
                {
                    $options = [];

                    $do = OSW3_V1::tryToDo($this->meta->options);
                    if (false != $do) $options = $do;

                    $this->options = $options;
                }
                else
                {
                    $this->options = $this->meta->options;
                }
            }
            else 
            {
                $this->options = null;
            }
        }
        protected function getOptions ()
        {
            $output = "";

            $value = !empty($this->getValue()) ? $this->getValue() : $this->meta->default;
            
            if (!empty( $this->attrPlaceholder ))
            {
                $output.= "<option value=\"\">".$this->attrPlaceholder."</option>";
            } else {
                $output.= "<option></option>";
            }
            
            if( $this->options )
            {
                foreach ( $this->options  as $key => $label)
                {
                    $selected = $key === $value ? " selected" : null;
                    $output.= "<option value=\"".$key."\"".$selected.">".$label."</option>";
                }
            }

            return $output;
        }
        
                
        protected function setAttrCols ()
        {
            $this->attrCols = isset($this->meta->cols) ? $this->meta->cols : null;
        }
        protected function getAttrCols ()
        {
            if (!empty($this->attrCols)) 
            {
                return 'cols="'.$this->attrCols.'"';
            }

            return null;
        }
        
                
        protected function setAttrRows ()
        {
            $this->attrRows = isset($this->meta->rows) ? $this->meta->rows : null;
        }
        protected function getAttrRows ()
        {
            if (!empty($this->attrRows)) 
            {
                return 'rows="'.$this->attrRows.'"';
            }

            return null;
        }
        
                
        protected function setAttrMultiple ()
        {
            $this->attrMultiple = isset($this->meta->multiple) ? $this->meta->multiple : null;
        }
        protected function getAttrMultiple ()
        {
            if (!empty($this->attrMultiple) && $this->attrMultiple == true) 
            {
                return 'multiple';
            }

            return null;
        }
        
        
        protected function setAttrRequired ()
        {
            $this->attrRequired = isset($this->meta->required) ? $this->meta->required : null;
        }
        protected function getAttrRequired ()
        {
            if (!empty($this->attrRequired) && $this->attrRequired == true) 
            {
                return 'required';
            }

            return null;
        }
        
        
        protected function setAttrChecked ()
        {
            $this->attrChecked = $this->getValue() === 'on';
        }
        protected function getAttrChecked ()
        {
            if (!empty($this->attrChecked) && $this->attrChecked == true) 
            {
                return 'checked';
            }

            return null;
        }
        
        
        protected function setAttrDisabled ()
        {
            $this->attrDisabled = isset($this->meta->disabled) ? $this->meta->disabled : null;
        }
        protected function getAttrDisabled ()
        {
            if (!empty($this->attrDisabled) && $this->attrDisabled == true) 
            {
                return 'disabled';
            }

            return null;
        }
        
        
        protected function setAttrReadonly ()
        {
            $this->attrReadonly = isset($this->meta->readonly) ? $this->meta->readonly : null;
        }
        protected function getAttrReadonly ()
        {
            if (!empty($this->attrReadonly) && $this->attrReadonly == true) 
            {
                return 'readonly';
            }

            return null;
        }
        
        
        protected function setAttrStep ()
        {
            $this->attrStep = isset($this->meta->precision) ? $this->meta->precision : null;
        }
        protected function getAttrStep ()
        {
            if (!empty($this->attrStep)) 
            {
                return 'step="'.$this->attrStep.'"';
            }

            return null;
        }
    }
}
