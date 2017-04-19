<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IvrController extends Controller
{
    public function answer(Request $request)
    {
        return [
            [
                'action'    => 'talk',
                'text'      => 'Welcome to the Laravel Hotline'
            ],
            [
                'action'    => 'talk',
                'text'      => "Press 1 to hear Taylor's latest tweet. Press 2 to listen to the latest Laravel Podcast",
                'bargeIn'   => true
            ],
            [
                'action'    => 'input',
                'eventUrl'  => [route('ivr.menu')],
                'maxDigits' => 1
            ]

        ];
    }

    public function menu(Request $request)
    {
        switch ($request->json('dtmf')){
            case '1';
                return $this->tweet($request);
            case '2':
                return $this->podcast($request);
            default:
                return $this->answer($request);
        }
    }

    public function tweet()
    {
        $tweet = \Twitter::getUserTimeline(['screen_name' => 'taylorotwell', 'count' => 1, 'format' => 'array'])[0];
        $text = $tweet['text'];

        foreach($tweet['entities']['urls'] as $link){
            $domain = parse_url($link['expanded_url'], PHP_URL_HOST);

            $text = substr($text, 0, $link['indices'][0]) .
                'and a link to ' . $domain .
                substr($text, $link['indices'][1]);
        }

        return [
            [
                'action' => 'talk',
                'text'   => \Twitter::ago($tweet['created_at']) . ' he tweeted ' . $text
            ]
        ];
    }

    public function podcast()
    {
        $rss = new \SimplePie();
        $rss->enable_cache(false);
        $rss->set_feed_url('https://rss.simplecast.com/podcasts/351/rss');
        $rss->init();

        $item = $rss->get_item(0);

        return [
            [
                'action' => 'talk',
                'text'   => $item->get_description()
            ],
            [
                'action' => 'stream',
                'streamUrl' => [$item->get_enclosure(0)->get_link()]
            ]
        ];
    }
}
