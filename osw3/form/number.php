<?php

if (!class_exists('OSW3_V1_NumberType'))
{
    class OSW3_V1_NumberType extends OSW3_V1_FormType
    {
        public function render($namespace, $showLabel = true, $wrapper = true, $before = null, $after = null)
        {
            return $this->renderField("number", $showLabel, $wrapper, [
                $this->getAttrType(),
                $this->getAttrName($namespace),
                $this->getAttrId($namespace),
                $this->getAttrClass(),
                $this->getAttrValue(),
                $this->getAttrPlaceholder(),
                $this->getAttrRequired(),
                $this->getAttrDisabled(),
                $this->getAttrStep(),
                $this->getAttrReadonly()
            ], $before, $after);
        }
    }
}
