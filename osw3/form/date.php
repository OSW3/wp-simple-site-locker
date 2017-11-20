<?php

if (!class_exists('OSW3_V1_DateType'))
{
    class OSW3_V1_DateType extends OSW3_V1_FormType
    {
        public function render($namespace, $showLabel = true, $wrapper = true, $before = null, $after = null)
        {
            return $this->renderField("text", $showLabel, $wrapper, [
                $this->getAttrType(),
                $this->getAttrName($namespace),
                $this->getAttrId($namespace),
                $this->getAttrClass(),
                $this->getAttrValue(),
                $this->getAttrPlaceholder(),
                $this->getAttrRequired(),
                $this->getAttrDisabled(),
                $this->getAttrReadonly()
            ], $before, $after);
        }
    }
}
