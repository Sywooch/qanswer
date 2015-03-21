<?php

namespace app\modules\question\models;

use app\models\Post;

class Answer extends Post
{
    public function init()
    {
        parent::init();
        $this->idtype = self::IDTYPE_A;
    }

    public function scenarios()
    {
        return [
        ];
    }

    public function rules()
    {
        return [
            ['content', 'required', 'on' => 'answer'],
//			['wiki','in','range'=>array(0,1)]
        ];
    }
    
    public function isAccepted()
    {
        return $this->accepted == self::ACCEPTED;
    }
}
