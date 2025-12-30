<?php
function convertUrlQuery($url)
{
    $res = parse_url($url);

    if (!$res['query']) return [];

    $query = explode('&', $res['query']);

    $params = array();

    foreach ($query as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}

function array2Query($arr)
{
    $keys = [];
    foreach ($arr as $key => $val) {
        array_push($keys, $key . '=' . $val);
    }

    return join('&', $keys);
}

class Paginator
{
    // 总数据量
    private $total = 0;
    // 当前页
    private $page = 1;
    // 分页大小
    private $pageSize = 0;
    // 页数
    private $pageCount = 0;
    // 显示页数
    public $pagerCount = 10;
    // 候选页码
    private $pageSizes = [5, 10, 20, 30, 40];

    private $querys = [];

    public $prevText = 'Prve';
    public $nextText = 'Next';

    private $queryPageField = 'page';
    private $queryPageSizeField = 'pageSize';

    public $hasPrevaore = false;
    public $hasNextMore = false;

    public $containerClassName = '';

    function __construct($total, $pageSize = 10)
    {
        $this->total = abs($total);
        $this->pageSize = $pageSize ? abs($pageSize) : 10;

        $this->setQueryField();
        $this->resize();
    }

    function setQueryField($arr = [])
    {
        if (array_key_exists('page', $arr) && $arr['page']) {
            $this->queryPageField = $arr['page'];
        }

        if (array_key_exists('size', $arr) && $arr['size']) {
            $this->queryPageSizeField = $arr['size'];
        }

        $params = convertUrlQuery($_SERVER['REQUEST_URI']);
        // 去除分页查询字符串
        unset($params[$this->queryPageField]);
        unset($params[$this->queryPageSizeField]);

        $this->querys = $params;

        // 设置当前页码
        if (array_key_exists($this->queryPageField, $_GET) && $_GET[$this->queryPageField]) {
            $this->page = abs($_GET[$this->queryPageField]);
        } else {
            $this->page = 1;
        }
    }

    function setQueryParams($arr)
    {
        if (is_array($arr)) {
            foreach ($arr as $key => $val) {
                $this->querys[$key] = $val;
            }
        }

        return $this->querys;
    }

    private function resize()
    {
        $this->pageCount = intval(ceil($this->total / $this->pageSize));

        if ($this->page > $this->pageCount) {
            $this->page = $this->pageCount;
        }
    }

    public function setPageSizes($sizes)
    {
        if (!is_array($sizes)) {
            throw new Exception('One param must be an Array.');
        }

        $this->pageSizes = $sizes;
    }

    // 计算分页页码
    private function pager()
    {
        $pagerCount = $this->pagerCount;
        $pageCount = $this->pageCount;
        $currentPage = $this->page;

        $halfPagerCount = intval(floor(($pagerCount - 1) / 2));

        $showPrevaore = false;
        $showNextMore = false;


        if ($pageCount > $pagerCount) {
            if ($currentPage > $pagerCount - $halfPagerCount) {
                $showPrevaore = true;
            }
            if ($currentPage < $pageCount - $halfPagerCount) {
                $showNextMore = true;
            }
        }

        $pagers = [];

        if ($showPrevaore && !$showNextMore) {
            $startPage = $pageCount - ($pagerCount - 2);
            for ($i = $startPage; $i < $pageCount; $i++) {
                array_push($pagers, intval($i));
            }
        } else if (!$showPrevaore && $showNextMore) {
            for ($i = 2; $i < $pagerCount; $i++) {
                array_push($pagers, intval($i));
            }
        } else if ($showPrevaore && $showNextMore) {
            $offset = floor($pagerCount / 2) - 1;
            for ($i = $currentPage - $offset; $i <= $currentPage + $offset; $i++) {
                array_push($pagers, intval($i));
            }
        } else {
            for ($i = 2; $i < $pageCount; $i++) {
                array_push($pagers, intval($i));
            }
        }

        $this->hasPrevaore = $showPrevaore;
        $this->hasNextMore = $showNextMore;

        return $pagers;
    }

    function linkUrl($page)
    {
        $path = strtok($_SERVER["REQUEST_URI"], '?');
        $queryString = array2Query($this->querys);

        $params = [
            $this->queryPageField . '=' . $page,
            $this->queryPageSizeField . '=' . $this->pageSize
        ];

        $url = $path . '?' . join('&', $params) . ($queryString ? '&' . $queryString : '');

        return $url;
    }

    function link($page, $text = null)
    {
        $url = $this->linkUrl($page);

        return '<a href="' . $url . '">' . ($text ? $text : $page) . '</a>';
    }

    // 页码部分html
    private function pagerHTML()
    {
        $pagers = $this->pager();
        $htmls = ['<ul class="m-pager">'];
        $pagerCountOffset = $this->pagerCount - 2;

        if ($this->pageCount > 0) {
            array_push($htmls, '<li class="m-pager-number' . ($this->page === 1 ? ' active' : '') . '">' . $this->link('1') . '</li>');
        }

        if ($this->hasPrevaore) {
            array_push($htmls, '<li class="m-pager-number">' . $this->link($this->page - $pagerCountOffset, '&laquo;') . '</li>');
        }

        $len = count($pagers);

        for ($i = 0; $i < $len; $i++) {
            array_push($htmls, '<li class="m-pager-number' . ($this->page === $pagers[$i] ? ' active' : '') . '">' . $this->link($pagers[$i]) . '</li>');
        }

        if ($this->hasNextMore) {
            array_push($htmls, '<li class="m-pager-number">' . $this->link($this->page + $pagerCountOffset, '&raquo;') . '</li>');
        }

        if ($this->pageCount > 1) {
            array_push($htmls, '<li class="m-pager-number' . ($this->page === $this->pageCount ? ' active' : '') . '">' . $this->link($this->pageCount) . '</li>');
        }

        array_push($htmls, '</ul>');
        return join('', $htmls);
    }

    private function prevHTML()
    {
        $page = $this->page - 1;
        $page = $page < 1 ? 1 : $page;

        return '<span class="m-pagination-prev' . ($this->page === 1 ? ' disabled' : '') . '">' . $this->link($page, $this->prevText) . '</span>';
    }

    private function nextHTML()
    {
        $page = $this->page + 1;
        $page = $page > $this->pageCount ? $this->pageCount : $page;

        return '<span class="m-pagination-next' . ($this->page === $this->pageCount ? ' disabled' : '') . '">' . $this->link($page, $this->nextText) . '</span>';
    }

    // 输出分页
    function links($layouts = ['total', 'prev', 'pager', 'next'])
    {
        $layoutMap = [
            'total' => '<span class="m-pagination-total">共' . $this->total . '条</span>',
            'prev' => $this->prevHTML(),
            'pager' => $this->pagerHTML(),
            'next' => $this->nextHTML()
        ];

        $htmls = ['<div class="m-pagination is-background' . ($this->containerClassName ? ' ' . $this->containerClassName : '') . '">'];

        if (!is_array($layouts)) {
            throw new Exception('One params must be an Array.');
        }

        $len = count($layouts);

        for ($i = 0; $i < $len; $i++) {
            array_push($htmls, ($layoutMap[$layouts[$i]] ? $layoutMap[$layouts[$i]] : $layouts[$i]));
        }

        array_push($htmls, '</div>');

        return join('', $htmls);
    }

    // 简单分页
    function simpleLinks()
    {
        return $this->links(['prev', 'next']);
    }

    function getPageData()
    {
        $data = [
            'total' => $this->total,
            'page' => $this->page,
            'pageSize' => $this->pageSize,
            'pageCount' => $this->pageCount
        ];

        return $data;
    }
}
