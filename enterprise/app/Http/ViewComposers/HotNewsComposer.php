<?php

namespace App\Http\ViewComposers;

use App;
use App\Models\NewsModel;
use Illuminate\Contracts\View\View;

class HotNewsComposer
{

    public function compose(View $view)
    {

        $newsModel = new NewsModel;
        $posCode   = [
            'detail-top-news'  => 7,
            'detail-top-game'  => 7,
            'detail-top-video' => 7,
        ];

        $recommend = [];
        foreach ($posCode as $code => $num) {
            $recommend[$code] = $newsModel->getNewsByCode($code, 0, $num);
        }
        $data = $recommend;
        $view->with(compact("data"));
    }

}
