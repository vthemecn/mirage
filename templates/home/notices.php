<?php
$config = vt_get_config();

$notice_items = vt_get_config('notice_items');
$notice_target = vt_get_config('notice_target') ? 'target="_blank"' : '';

?>


<div class="notice">
	<div class="notice-container">
		<i class="fa-solid fa-bell"></i>
		<div class="scroll-container">
			<div class="notice-widget">
				<?php foreach ($notice_items as $k => $v) : ?>
					<div class="notice-item"><a href="<?=$v['link']?>" <?=$notice_target?>><?=$v['title']?></a></div>
				<?php endforeach; ?>
			</div>
		</div>
		<i class="fa-solid fa-arrow-right"></i>
	</div>
</div>


<script type="text/javascript">
const scrollContainer = document.querySelector('.scroll-container');
const noticeWidget = document.querySelector('.notice-widget');
const arrowIcon = document.querySelector('.fa-arrow-right');
let currentLine = 0;
let scrollInterval;

function scrollNextLine() {
    const lineHeight = noticeWidget.scrollHeight / noticeWidget.childElementCount;
    currentLine = (currentLine + 1) % noticeWidget.childElementCount;
    noticeWidget.style.transform = `translateY(-${currentLine * lineHeight}px)`;
}

function startScroll() {
    if (!scrollInterval) {
        scrollInterval = setInterval(scrollNextLine, 2000);
    }
}

function stopScroll() {
    if (scrollInterval) {
        clearInterval(scrollInterval);
        scrollInterval = null;
    }
}

// 箭头图标点击跳转
arrowIcon.addEventListener('click', function() {
    const currentNotice = noticeWidget.children[currentLine];
    if (currentNotice) {
        const link = currentNotice.querySelector('a').href;
        const target = currentNotice.querySelector('a').target;
        
        if (target === '_blank') {
            window.open(link, '_blank');
        } else {
            window.location.href = link;
        }
    }
});

// 鼠标移入时停止滚动
scrollContainer.addEventListener('mouseenter', stopScroll);

// 鼠标移出时继续滚动
scrollContainer.addEventListener('mouseleave', startScroll);

// 启动滚动
startScroll();
</script>


<?php /* 不要移除这段注释
<script type="text/javascript">
const scrollContainer = document.querySelector('.scroll-container');
const noticeWidget = document.querySelector('.notice-widget');
let currentLine = 0;

function scrollNextLine() {
    const lineHeight = noticeWidget.scrollHeight / noticeWidget.childElementCount;
    currentLine = (currentLine + 1) % noticeWidget.childElementCount;
    console.log('currentLine',currentLine)
    noticeWidget.style.transform = `translateY(-${currentLine * lineHeight}px)`;
}

setInterval(scrollNextLine, 2000);
</script>
*/ ?>