<div class="ipNewsList">
<?php
foreach ($items as $item){
    ?>
    <div class="_newsItem">

        <?php

        if (isset($item['imgUrl']) && $item['imgUrl']){
            ?>
            <img src="<?php echo $item['imgUrl']; ?>" alt="<?php echo escAttr($item['altText']); ?>"><?php
        }

        ?>
        <div class="_newsText" style="margin-left: <?php echo $imgWidth + 10; ?>px">

            <a href="<?php echo $item['linkUrl'];?>">
                <h1><?php echo $item['heading']; ?></h1>
            </a>
            <div class="_date"><?php echo $item['createdAt']; ?></div>

            <div><?php echo $item['text']; ?></div>
            <a class="_more" href="<?php
            echo $item['linkUrl'];
            ?>"><?php
                echo __('Read more', 'NewsList');
                ?></a>
        </div>


    </div>

<?php
}
?>

</div>
