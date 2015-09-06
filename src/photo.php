<?php
class Photo
{
    private $_photos = array(
        array(
            'file' => '2015-08-15_150521.jpg',
            'title'=> '',
            'tag'  => array(),
        ),
    );
    public function getIndex()
    {
        $html = '';
        $html .= '<div id="index_right">';
        $html .= "<img src=\"/photo/{$this->_photos[0]['file']}\">";
        $html .= '</div>';
        return $html;
    }
}
