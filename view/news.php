<div class="ipNewsList">
<?php
foreach ($items as $item){
    ?>
    <div class="_newsItem">

        <?php

        if (isset($item['imgUrl'])){

            ?>
            <img src="<?php $item['imgUrl']; ?>"<?php

                if (isset($imgWidth)){
                    echo ' width="'.$imgWidth.'"';
                }

            ?> alt="<?php echo $item['altText']; ?>"><?php

        }

        ?>
        <div class="_newsText">

            <a href="<?php echo $item['linkUrl'];?>">
                <h1><?php echo $item['heading']; ?></h1>
            </a>
            <div class="_date"><?php echo $item['createdAt']; ?></div>

            <div><?php echo $item['text']; ?></div>
        </div>

        <a class="_more" href="<?php
            echo $item['linkUrl'];
        ?>"><?php
            echo __('Read more', 'NewsList');
        ?></a>
    </div>

<?php
}
?>

</div>