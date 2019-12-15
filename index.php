<?php

class Curl
{
    /**
     * @var string
     */
    protected $url;

    /**
     * Curl constructor.
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return bool|string
     * @throws Exception
     */
    public function exec()
    {
        if ($this->url) {
            $curl = curl_init($this->url);
            curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
            $html = curl_exec($curl);
            curl_close ($curl);
            return $html;
        } else {
            throw new Exception("No URL for Curl");
        }
    }
}

trait Parser
{
    /**
     * @var string
     */
    protected $html;

    /**
     * Parser constructor.
     * @param string $html
     */
    public function __construct($html)
    {
        $this->html = $html;
    }

}

class MainPageParser
{
    use Parser;

    /**
     * @return array
     */
    public function getLinksForBroadcastPages()
    {
        $partWithTodayMatches = $this->findPartWithTodayMatches();
        if ($partWithTodayMatches) {
            return $this->findLinksForBroadcastPages($partWithTodayMatches);
        }
        return [];
    }

    /**
     * @return string
     */
    protected function findPartWithTodayMatches()
    {
        try {
            $today = new DateTime('now', new DateTimeZone('Europe/Moscow'));
            $todayDayNumber = (int)$today->format('d');
            $tomorrowDayNumber = $todayDayNumber + 1;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit();
        }

        $pattern = '#<div class="streams-day">' . $todayDayNumber . '(.*)#';
        preg_match($pattern, $this->html, $matches, PREG_OFFSET_CAPTURE);

        if (isset($matches[1])) {
            $startPosition = $matches[1][1];

            $pattern = '#<div class="streams-day">' . $tomorrowDayNumber . '(.*)#';
            preg_match($pattern, $this->html, $matches, PREG_OFFSET_CAPTURE);

            $endPosition = isset($matches[1]) ? $matches[1][1] : null;
            return mb_strcut($this->html, $startPosition, $endPosition);
        } else {
            return '';
        }
    }

    /**
     * @param string $partWithTodayMatches
     * @return array|mixed
     */
    protected function findLinksForBroadcastPages($partWithTodayMatches)
    {
        $pattern = '#<a href="/broadcast/football/(.*)" rel="bookmark">(.*)</a>#';
        $numberOfMatches = preg_match_all($pattern, $this->html, $matches);

        if ($numberOfMatches > 0) {
            return array_map(function ($link, $name) {
                return [
                    'name' => $name,
                    'link' => $link,
                ];
            }, $matches[1], $matches[2]);
        }
        return [];
    }
}

class MatchPageParser
{
    use Parser;

    /**
     * @return string
     */
    public function getLinkForAceStreamBroadcast()
    {
        return 'link';
        $pattern = '##';
        preg_match($pattern, $this->html, $matches, PREG_OFFSET_CAPTURE);
    }
}

class App
{
    const PIMPLE_BASE_URL = 'https://www.pimpletv.ru/category/broadcast/football/';

    public function init()
    {
        ini_set('max_execution_time', 0); // 0 hrs
        ini_set('memory_limit', '500M'); // 500 Mb
        $curl = new Curl(self::PIMPLE_BASE_URL);
        $html = $curl->exec();

        $matchPageParser = new MainPageParser($html);
        $linksForBroadcastPages = $matchPageParser->getLinksForBroadcastPages();
        $linksForBroadcasts = [];

        foreach ($linksForBroadcastPages as $item) {
            $curl = new Curl(self::PIMPLE_BASE_URL . $item['link']);
            $html = $curl->exec();
            echo $html; exit();

            $matchPageParser = new MatchPageParser($html);
            $linksForBroadcasts[] = [
                'name' => $item['name'],
                'acestream' => $matchPageParser->getLinkForAceStreamBroadcast(),
            ];
        }

        return $linksForBroadcasts;
    }
}

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
$app = new App();
$data = $app->init();

echo json_encode($data);

