<?php

if (!class_exists('OSW3_V1_TextareaType'))
{
    class OSW3_V1_TextareaType extends OSW3_V1_FormType
    {
        public function render($namespace, $showLabel = true, $wrapper = true, $before = null, $after = null)
        {
            return $this->renderField("textarea", $showLabel, $wrapper, [
                $this->getAttrName($namespace),
                $this->getAttrId($namespace),
                $this->getAttrClass(),
                $this->getAttrPlaceholder(),
                $this->getAttrCols(),
                $this->getAttrRows(),
                $this->getAttrRequired(),
                $this->getAttrDisabled(),
                $this->getAttrReadonly()
            ], $before, $after, $this->getValue());
        }
    }
}
