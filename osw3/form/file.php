<?php

if (!class_exists('OSW3_FileType'))
{
    class OSW3_FileType extends OSW3_FormType
    {
        // public function render($namespace, $showLabel = true, $wrapper = true, $before = null, $after = null)
        public function render()
        {
            $value = $this->getValue();

            if (null != $value && is_array($value) && isset($this->meta->preview) && $this->meta->preview)
            {

                // var_dump($value);

                $before .= "<div>";
                $before .= wp_get_attachment_image($value['attachment']);
                $before .= "</div>";
            }

            return $this->renderField([
                $this->getAttrType(),
                $this->getAttrName(),
                $this->getAttrId(),
                $this->getAttrClass(),
                $this->getAttrRequired(),
                $this->getAttrDisabled(),
                $this->getAttrReadonly()
            ]);
            // return $this->renderField("file", $showLabel, $wrapper, [
            //     $this->getAttrType(),
            //     $this->getAttrName($namespace),
            //     $this->getAttrId($namespace),
            //     $this->getAttrClass(),
            //     $this->getAttrRequired(),
            //     $this->getAttrDisabled(),
            //     $this->getAttrReadonly()
            // ], $before, $after);
        }
    }
}
